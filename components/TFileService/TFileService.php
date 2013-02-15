<?php

class TFileService extends TDBService {
    /* SRVDEF */

    private static $_definition_struc = array(
        'models' => array(
            'userfiles' => array(
                'p' => 'a:3:{s:3:"idx";a:2:{i:0;i:1;i:1;b:0;}s:7:"filters";a:2:{i:0;i:3;i:1;b:0;}s:5:"order";a:2:{i:0;i:3;i:1;b:0;}}',
                'i' => 'a:9:{s:6:"origin";a:2:{i:0;i:3;i:1;b:0;}s:8:"filesize";a:2:{i:0;i:1;i:1;b:0;}s:8:"filetype";a:2:{i:0;i:3;i:1;b:0;}s:9:"extention";a:2:{i:0;i:3;i:1;b:0;}s:5:"title";a:2:{i:0;i:3;i:1;b:0;}s:4:"tags";a:2:{i:0;i:3;i:1;b:0;}s:4:"text";a:2:{i:0;i:3;i:1;b:0;}s:6:"rights";a:2:{i:0;i:3;i:1;b:0;}s:4:"data";a:2:{i:0;i:3;i:1;b:1;}}',
                'u' => 'a:9:{s:6:"origin";a:2:{i:0;i:3;i:1;b:0;}s:8:"filetype";a:2:{i:0;i:3;i:1;b:0;}s:4:"date";a:2:{i:0;i:3;i:1;b:0;}s:2:"ip";a:2:{i:0;i:3;i:1;b:0;}s:5:"title";a:2:{i:0;i:3;i:1;b:0;}s:4:"tags";a:2:{i:0;i:3;i:1;b:0;}s:4:"text";a:2:{i:0;i:3;i:1;b:0;}s:5:"owner";a:2:{i:0;i:1;i:1;b:0;}s:6:"rights";a:2:{i:0;i:3;i:1;b:0;}}',
                'f' => 'a:15:{s:3:"idx";i:1;s:6:"origin";i:3;s:8:"filesize";i:1;s:8:"filetype";i:3;s:9:"extention";i:3;s:5:"size1";i:1;s:5:"size2";i:1;s:4:"date";i:3;s:2:"ip";i:3;s:5:"title";i:3;s:4:"tags";i:3;s:4:"text";i:3;s:5:"owner";i:1;s:6:"rights";i:3;s:4:"path";i:3;}',
                'owner' => true,
            ),
        ),
        'commands' => array(
        ),
        'links' => array(
            'userfiles' => array(
                'owner' => 'TAccountService.users',
            ),
        ),
    );

    protected function &getDefinitionStruc() {
        return self::$_definition_struc;
    }

    /* SRVDEF */

    const ACCOUNT_SRV_REQUIRED = 300;
    const USER_REQUIRED = 301;
    const CREATE_DIRECTORY = 302;
    const FILE_SIZE_LIMIT = 303;
    const EXT_NOT_ALLOWED = 304;
    const DBERROR = 305;
    const DEFAULT_DIR_MODE = 0755; //rvk
    const DEFAULT_FILE_MODE = 0644; //rvk

    public function _fetch_userfiles_model($args) {
        $this->_getSQLWhere($args, array('idx'), $where, $params);
        $r = $this->_fetchTableModel($args, 'userfiles', $where, $params);
        return $r;
    }

    public function _insert_userfiles_model($args) {
        $v = $args['values'];
        $uidx = $this->project->user_id;
        if (($uidx == 0) && $this->auth_required)
            self::error(self::USER_REQUIRED);
        $m_bytes = round(strlen(rtrim($v['data'], '=')) * 3 / 4194304, 2);
        if ($m_bytes > $this->max_size_MB)
            self::error(self::FILE_SIZE_LIMIT, $m_bytes . 'MB>' . $this->max_size_MB . 'MB'); //exeption on file_size
        $v['owner'] = $uidx;
        $v['date'] = date(self::SQL_DATE_FORMAT, time());
        $v['ip'] = $_SERVER['REMOTE_ADDR'];
        $v_data = $v['data'];
        unset($v['data']);
        $this->file_ext = strtolower(substr(strrchr($v['origin'], '.'), 1));
        if (array_search($this->file_ext, $this->allowed_ext) === false)
            self::error(self::EXT_NOT_ALLOWED, $this->file_ext); //exeption on file_ext
        $v['extention'] = $this->file_ext;
        $this->_insertTableModel($v, 'userfiles');
        $v['data'] = $v_data;
        $files_dir = $this->name;
        $path = $this->project->path . '/data/' . $files_dir;
        $this->last_id = $this->db->lastInsertId();

        if ($this->anonim_file_name === 1) {
            list($dir, $fname) = $this->_getFileName($this->last_id, $this->file_ext);
            $path.= '/'.$dir;
            if (!file_exists($path)) {
                if (!mkdir($path, self::DEFAULT_DIR_MODE, true))
                    self::error(self::CREATE_DIRECTORY, $path);
            }
            $file = $path . '/' . $fname;
            $fileURL = $dir . '/' . $fname;
        }
        else {
            $file = $path . '/' . $v['origin'];
            $fileURL = $v['origin'];
        }
        $table = $this->name . '_userfiles';
        if (file_put_contents($file, base64_decode($v['data']))) {
            $sql = "UPDATE `$table`  SET path='$fileURL' WHERE idx = '$this->last_id'";
        } else {
            $sql = "DELETE FROM `$table` WHERE idx = '$this->last_id'";
        }
        if (self::$this->db->query($sql) === false)
            self::_dbError();
    }

    public function _remove_userfiles_model($args) {
        $table = $this->name . '_userfiles';
        $cmd = $this->_exec("SELECT path FROM `$table` WHERE idx=".$args['index']);
        $rows = $cmd->fetchAll(PDO::FETCH_ASSOC);
        array_map("unlink", glob($this->project->path . '/data/' . $this->name . '/' . $rows[0]['path']));
        $this->_removeTableModel($args['index'], 'userfiles');
    }

    protected function _getFileName($id, $ext) {
        $dir = "R" . str_pad(round($id / 100), 7, '0', STR_PAD_LEFT);
        $fname = str_pad($id, 7, '0', STR_PAD_LEFT) . "." . $ext;
        return array($dir, $fname);
    }

    protected static function _getErrorMsg($args) {
        $code = $args[0];
        switch ($code) {
            case self::CREATE_DIRECTORY: {
                    $msg = 'Can not create directory: ' . $args[1];
                    break;
                }
            case self::FILE_SIZE_LIMIT: {
                    $msg = 'File size limit exceeded: ' . $args[1];
                    break;
                }
            case self::EXT_NOT_ALLOWED: {
                    $msg = 'File extention is not allowed: ' . $args[1];
                    break;
                }
            case self::USER_REQUIRED: {
                    $msg = "Authorization required";
                    break;
                }
            case self::DBERROR: {
                    $msg = 'Database error: ' . $args[1];
                    break;
                }
            default: $msg = parent::_getErrorMsg($args);
        }
        return $msg;
    }

}

?>
