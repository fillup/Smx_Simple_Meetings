<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\Base;
use Smx\SimpleMeetings\Base\Site;

/**
 * Attendee class provides methods for adding and listing attendees.
 * 
 * Attendee class extends the Site class in order to inherit common fields
 * such as username, password, and sitename.  Base class primariy provides
 * getter/setter methods and each adapter that extends Attendee must implement
 * actual functionality for interacing with APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Attendee extends Site
{
    public $name = null;
    public $email = null;
    public $meetingKey = null;
    public $attendeeId = null;
    
    /**
     * 
     * @param String $username
     * @param String $password
     * @param String $sitename
     * @param Array $options
     */
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
