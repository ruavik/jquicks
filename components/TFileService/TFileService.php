<?php
class TFileService extends TDBService{
/*SRVDEF*/
private static $_definition_struc = array (
  'models' =>array (
    'userfiles' =>array (
      'p' => 'a:3:{s:3:"idx";a:2:{i:0;i:1;i:1;b:0;}s:7:"filters";a:2:{i:0;i:3;i:1;b:0;}s:5:"order";a:2:{i:0;i:3;i:1;b:0;}}',
      'i' => 'a:3:{s:5:"title";a:2:{i:0;i:3;i:1;b:0;}s:4:"text";a:2:{i:0;i:3;i:1;b:0;}s:6:"rights";a:2:{i:0;i:3;i:1;b:0;}}',
      'u' => 'a:7:{s:6:"origin";a:2:{i:0;i:3;i:1;b:0;}s:4:"date";a:2:{i:0;i:3;i:1;b:0;}s:2:"ip";a:2:{i:0;i:3;i:1;b:0;}s:5:"title";a:2:{i:0;i:3;i:1;b:0;}s:4:"text";a:2:{i:0;i:3;i:1;b:0;}s:5:"owner";a:2:{i:0;i:1;i:1;b:0;}s:6:"rights";a:2:{i:0;i:3;i:1;b:0;}}',
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
}
?>
