<?php

namespace app;

Class Api {
    
    public $method;
    public $message = array();
    
    
    public function __construct() {
        $this->method=filter_var(getenv('REQUEST_METHOD'));
    }
   
    public function setResponseError($msg) {
        $this->message['error'] = $msg;
        return true;
    }
    public function setResponseFiles($array){
        foreach ($array as $value) {
             $this->message['files'][] = $array;
        }
        return true;
    }
    public function setResponseData ($key, $val) {
        $this->message[$key] = $val;
    }
    public function sendResponse() {
        echo json_encode($this->message);
    }
    
}

