<?php

namespace Smx\SimpleMeetings\Base;
use Smx\SimpleMeetings\Base\Site;

class Attendee extends Site
{
    public $name = null;
    public $email = null;
    public $meetingKey = null;
    public $attendeeId = null;
    
    public function __construct($username = false, $password = false, 
            $sitename = false, $options = false) {
        parent::__construct($username, $password, $sitename);
        if($options){
            if(isset($options['name'])){
                $this->name = $options['name'];
            }
            if(isset($options['email'])){
                $this->email = $options['email'];
            }
            if(isset($options['meetingKey'])){
                $this->meetingKey = $options['meetingKey'];
            }
        }
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function setEmail($email){
        $this->email = $email;
    }
    
    public function getEmail(){
        return $this->email;
    }
    
    public function setMeetingKey($meetingKey){
        $this->meetingKey = $meetingKey;
    }
    
    public function getMeetingKey(){
        return $this->meetingKey;
    }
    
    public function setAttendeeId($attendeeId){
        $this->attendeeId = $attendeeId;
    }
    
    public function getAttendeeId(){
        return $this->attendeeId;
    }
}
