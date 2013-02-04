<?php
class TFileService extends TDBService{
/*SRVDEF*/
private static $_definition_struc = array (
  'models' =>array (
    'userfiles' =>array (
      'p' => 'a:3:{s:3:"idx";a:2:{i:0;i:1;i:1;b:0;}s:7:"filters";a:2:{i:0;i:3;i:1;b:0;}s:5:"order";a:2:{i:0;i:3;i:1;b:0;}}',
      'i' => 'a:7:{s:6:"origin";a:2:{i:0;i:3;i:1;b:0;}s:8:"filesize";a:2:{i:0;i:1;i:1;b:0;}s:8:"filetype";a:2:{i:0;i:3;i:1;b:0;}s:5:"title";a:2:{i:0;i:3;i:1;b:0;}s:4:"text";a:2:{i:0;i:3;i:1;b:0;}s:6:"rights";a:2:{i:0;i:3;i:1;b:0;}s:4:"data";a:2:{i:0;i:3;i:1;b:1;}}',
      'u' => 'a:8:{s:6:"origin";a:2:{i:0;i:3;i:1;b:0;}s:8:"filetype";a:2:{i:0;i:3;i:1;b:0;}s:4:"date";a:2:{i:0;i:3;i:1;b:0;}s:2:"ip";a:2:{i:0;i:3;i:1;b:0;}s:5:"title";a:2:{i:0;i:3;i:1;b:0;}s:4:"text";a:2:{i:0;i:3;i:1;b:0;}s:5:"owner";a:2:{i:0;i:1;i:1;b:0;}s:6:"rights";a:2:{i:0;i:3;i:1;b:0;}}',
      'f' => 'a:12:{s:3:"idx";i:1;s:6:"origin";i:3;s:8:"filesize";i:1;s:8:"filetype";i:3;s:5:"size1";i:1;s:5:"size2";i:1;s:4:"date";i:3;s:2:"ip";i:3;s:5:"title";i:3;s:4:"text";i:3;s:5:"owner";i:1;s:6:"rights";i:3;}',
      'owner' => true,
    ),
  ),
  'commands' =>array (
  ),
  'links' =>array (
    'userfiles' =>array (
      'owner' => 'TAccountService.users',
    ),
  ),
);
protected function &getDefinitionStruc(){return self::$_definition_struc;}
/*SRVDEF*/
    const ACCOUNT_SRV_REQUIRED = 300;
    const USER_REQUIRED = 301;
    const SQL_DATE_FORMAT = 'Y-m-d h:i:s';

    
    public function createTable($name){
        $path = $this->project->path.'/data/'.$this->name;
        if(!file_exists($path)) { if(!mkdir($path)) self::error(self::CREATE_DIRECTORY,$path);}
        parent::createTable($name);
    }
    
    public function _fetch_userfiles_model($args){
        $this->_getSQLWhere($args, array('idx'), $where, $params);
        $r = $this->_fetchTableModel($args, 'userfiles', $where, $params);
        return $r;
    }
    public function _insert_userfiles_model($args){
        $v = $args['values'];
        $uidx = $this->project->user_id;
        //if(($uidx == 0) && $this->auth_required) self::error(self::USER_REQUIRED);
        $v['owner'] = $uidx;
        $v['date'] = date(self::SQL_DATE_FORMAT,time());
        $v['ip'] =$_SERVER['REMOTE_ADDR'];
        $this->_insertTableModel($v, 'userfiles');
        $this->last_id = $this->db->lastInsertId(); 
        $this->file_ext = substr($v['origin'], strrpos($v['origin'], '.') + 1);
        //exeption on file_ext
        list($dir,$fname) = $this->_getFileName();
        $path = $this->project->path.'/data/'.$this->name.'/'.$dir;
        if(!file_exists($path)) { if(!mkdir($path)) self::error(self::CREATE_DIRECTORY,$path);}
        $file = $path.'/'.$fname;
        file_put_contents ($file, base64_decode($v['data']));
    }
    	protected function _getFileName(){
		$dir = "R".str_pad(round($this->last_id/100),7, '0', STR_PAD_LEFT);
		$fname = str_pad($this->last_id,7, '0', STR_PAD_LEFT).".".$this->file_ext;
		return array($dir,$fname);
	}
}
?>
