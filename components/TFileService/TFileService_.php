<?php

class TFileService_ extends TDBService_ {

    const RENAME_DIRECTORY = 301;
    const CREATE_DIRECTORY = 302;
    const DEFAULT_DIR_MODE = 0755;
    const DEFAULT_FILE_MODE = 0644;

    public function insertIntoProject($parent, $section, $order, $page, $name = null) {
        parent::insertIntoProject($parent, $section, $order, $page, $name = null);
        $files_dir = $this->project->db['components'][$this->id]['n'];
        $this->makeRepositoryDir($files_dir);
    }

    public function makeRepositoryDir($files_dir) {
        $path = $this->project->path . '/data/' . $files_dir;
        if (file_exists($path))
            return;
        if (!file_exists($path)) {
            if (!mkdir($path, self::DEFAULT_DIR_MODE, true))
                self::error(self::CREATE_DIRECTORY, $path);
        }
    }

    public function renameRepositoryDir($oldname, $newname) {
        $path = $this->project->path . '/data/';
        if (file_exists($path . $newname))
            return; //если случайно удалить файловую службу 
                    //или компонент со встроенной файловой службой, 
                    //то таблица базы данных и директория с файлами не удаляются 
                    //можно создать компонент заново с таким же именем - без потери данных

        if (!rename($path . $oldname, $path . $newname))
            self::error(self::RENAME_DIRECTORY, $oldname);
    }

    protected function _rename($name) {
        $oldname = strtolower($this->_getName());
        $newname = strtolower($name);
        parent::_rename($name);
        $this->renameRepositoryDir($oldname, $newname);
    }

    protected static function _getErrorMsg($args) {
        $code = $args[0];
        switch ($code) {
            case self::CREATE_DIRECTORY: {
                    $msg = 'Can not create directory: ' . $args[1];
                    break;
                }
            case self::RENAME_DIRECTORY: {
                    $msg = 'Can not rename directory: ' . $args[1];
                    break;
                }
            default: $msg = parent::_getErrorMsg($args);
        }
        return $msg;
    }

}
