<?php

class TFLoader extends TVidget {
    // Error codes
    const MODEL_NOT_FOUND = 201;
    const SERVICE_NOT_FOUND = 202;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                protected $client_fields = array('allowed_ext', 'max_size_MB', 'max_concurrent', 'files_dir','view_model','show_loader');

    protected function isClientInstance() {
        return true;
    }

    public function __construct($project,$struc=null){
        
        parent::__construct($project,$struc);
        if($this->view_model == "") self::error(self::MODEL_NOT_FOUND, $this->name);
        //$project= Jq::$project;
        $model= $project->getById($project->db['names'][$this->view_model]);
        if(!isset($project->db['names'][$model->service])) self::error(self::SERVICE_NOT_FOUND, $this->name, $model->service);
        $service_id = $project->db['names'][$model->service];
        
        $service= $project->getById($service_id);
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
            case self::SERVICE_NOT_FOUND: {$msg = 'Service '.$args[2].' not found. Component: '.$args[1]; break;}
            default: $msg = parent::_getErrorMsg($args);
        }
        return $msg;
    }
}

?>