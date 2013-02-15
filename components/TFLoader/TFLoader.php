<?php

class TFLoader extends TVidget {
    // Error codes
    const MODEL_NOT_FOUND = 201;
    
    protected $client_fields = array('allowed_ext', 'max_size_MB', 'max_concurrent', 'files_dir','view_model','show_loader');

    protected function isClientInstance() {
        return true;
    }

    public function __construct($struc=null){
        
        parent::__construct($struc);
        if($this->view_model == "") self::error(self::MODEL_NOT_FOUND, $this->name);
        $project= Jq::$project;
        $model= $project->getById($project->db['names'][$this->view_model]);
        $service= $project->getById($project->db['names'][$model->service]);
        $this->allowed_ext = $service->allowed_ext; 
        $this->max_concurrent = $service->max_concurrent; 
        $this->max_size_MB = $service->max_size_MB; 
        $this->files_dir = $service->name; 
        $this->view_model = $this->view_model; 
    }
    protected static function _getErrorMsg($args){
        $code = $args[0];
        switch ($code){
            case self::MODEL_NOT_FOUND: {$msg = 'Model not found. Component: '.$args[1]; break;}
            default: $msg = parent::_getErrorMsg($args);
        }
        return $msg;
    }
}

?>