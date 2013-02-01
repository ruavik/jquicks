<?php
class TFileService_ extends TDBService_{
    const CREATE_DIRECTORY = 300;
    const RENAME_PAGE_FILE = 301;
    
        protected function _rename($name) {
        $oldname = strtolower($this->_getName());
        $newname = strtolower($name);
        parent::_rename($name);
        $path = $this->project->path;
        if(!rename($path.'/data/'.$oldname,$path.'/data/'.$newname)) self::error(self::RENAME_PAGE_FILE,$oldname);
        
    }
}
