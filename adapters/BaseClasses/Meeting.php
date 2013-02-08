<?php

namespace Smx\SimpleMeetings\Base;

class Meeting {
    public $isPublic = false;
    public $enforcePassword = true;
    public $meetingPassword = null;
    public $meetingName = null;
    public $startTime = null;
    public $duration = 60;
    public $hostUsername = null;
    public $hostPassword = null;
    public $sitename = null;
    public $meetingKey = null;
    
    public function __construct($hostUsername, $hostPassword, $sitename, $options=false) {
        $this->hostUsername = $hostUsername;
        $this->hostPassword = $hostPassword;
        $this->sitename = $sitename;
        
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        if(is_null($this->meetingName)){
            $this->meetingName = $this->hostUsername."'s meeting";
        } elseif(is_null($this->startTime)){
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
