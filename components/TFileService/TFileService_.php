<?php
class TFileService_ extends TDBService_{
    const RENAME_PAGE_FILE = 301;
    
        protected function _rename($name) {
        $oldname = strtolower($this->_getName());
        $newname = strtolower($name);
        parent::_rename($name);
        $path = $this->project->path;
        if(!rename($path.'/data/'.$oldname,$path.'/data/'.$newname)) self::error(self::RENAME_PAGE_FILE,$oldname);
        
    }
    protected static function _getErrorMsg($args){
        $code = $args[0];
        switch ($code){
            case self::RENAME_PAGE_FILE: {$msg = 'Can not rename page file: '.$args[1].'.php'; break;}
            default: $msg = parent::_getErrorMsg($args);
        }
        return $msg;
    }    
}
