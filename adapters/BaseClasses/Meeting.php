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
 * Basic meeting class to represent a meeting/session.
 * 
 * Provides methods for scheduling, editing, deleting, listing, etc. Base class
 * models the meeting and provides common methods, however each adapter must
 * individually implement API calls to perform actions.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
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
    
    /**
     * History details is an array of the actual meeting usage data for a meeting
     * that has taken place. The expected fields are startTime, endTime, duration,
     * totalParticipants, totalPeopleMinutes, totalVoip, totalPhone
     */
    public $historyDetails = array();
    
    /**
     * 
     * @param string $username
     * @param string $password
     * @param string $sitename
     * @param array $options
     */
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
    
    public function setOptions($options){
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
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
