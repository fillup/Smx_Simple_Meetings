<?php

namespace Smx\SimpleMeetings\Base;
use Smx\SimpleMeetings\Base\Site;

class Meeting extends Site {
    public $isPublic = false;
    public $enforcePassword = true;
    public $meetingPassword = null;
    public $meetingName = null;
    public $startTime = null;
    public $duration = 60;
    public $meetingKey = null;
    public $hostUrl = null;
    public $joinUrl = null;
    
    public function __construct($username, $password, $sitename, $options=false) {
        parent::__construct($username, $password, $sitename);
        
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        if(is_null($this->meetingName)){
            $this->meetingName = $this->getUsername()."'s meeting";
        }
        if(is_null($this->startTime)){
            $this->startTime = date('m/d/Y H:i:00',time()+300);
        }
    }
    
    public function setOption($name,$value){
        $this->$name = $value;
    }
    
    public function getOption($name){
        if(isset($this->$name)){
            return $this->$name;
        } else {
            throw new \UnexpectedValueException(
                    "Property named '$name' not current set on object.",
                    100
                    );
        }
    }
}
