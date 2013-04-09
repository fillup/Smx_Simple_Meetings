<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\JoinMe;
use Smx\SimpleMeetings\JoinMe\Account;
use Smx\SimpleMeetings\JoinMe\Utilities;
use Smx\SimpleMeetings\Shared\ItemList;

/**
 * JoinMe Meetings class. Adds functionality for calling JoinMe APIs.
 * 
 * JoinMe is a very simple instant desktop sharing service and does not have 
 * many of the advanced features of other services. Therefore this adapter only 
 * implements the most basic ability to authenticate a user and create a meeting.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Meeting extends Account implements \Smx\SimpleMeetings\Interfaces\Meeting
{
    public $meetingKey = null;
    public $meetingPassword = null;
    public $hostPassword = null;
    public $hostUrl = null;
    public $joinUrl = null;
    
    // Not needed for JoinMe but present for cross compatibility
    public $meetingName = null;
    public $startTime = null;
    public $duration = null;
    
    public function __construct($authInfo) {
        parent::__construct($authInfo);
    }
    
    /**
     * Create a Join.Me download/start URL
     * 
     * Method calls API to get a start meeting code and ticket. You only need 
     * to call this method and then access the hostUrl and joinUrl properties 
     * to get the links for the host to download the software and start the meeting
     * and the link for an attendee to join the share.
     * 
     * @param array $options Unused with this adapter.
     * @return \Smx\SimpleMeetings\JoinMe\Meeting A copy of $this
     */
    public function createMeeting($options=false){
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        if($this->isAuthenticated()){
            $url = 'https://secure.join.me/API/requestCode?authcode='.$this->getAuthCode();
            $results = Utilities::callApi($url);
            $success = preg_match('/CODE: (\d+)[\n]TICKET: (\d+)/', $results, $details);
            if($success){
                $this->meetingKey = $details[1];
                $this->meetingPassword = $details[2];
                $this->hostPassword = $details[2];
                
                $this->startMeeting();
                $this->joinMeeting();

                return $this;
            } else {
                throw new \ErrorException('Unable to create meeting, error: '.$results, 301);
            }
        } else {
            throw new \ErrorException('User must be logged in with a valid auth 
                code before creating a meeting.',302);
        }
    }
    
    /**
     * Dummy method to satisfy interface
     * 
     * @return boolean
     */
    public function getServerMeetingDetails(){
        return false;
    }
    
    /**
     * Dummy method to satisfy interface
     * 
     * @param array $options Unused
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getMeetingList($options=false){
        return new ItemList();
    }
    
    /**
     * Retrieve the hostUrl for starting the session.
     * 
     * @param boolean $urlOnly
     * @return \Smx\SimpleMeetings\JoinMe\Meeting|string
     */
    public function startMeeting($urlOnly=false){
        $this->hostUrl = 'https://secure.join.me/download.aspx?webdownload=true'.
                        '&code='.$this->meetingKey.'&ticket='.$this->meetingPassword;
        if($urlOnly){
            return $this->hostUrl;
        } else {
            return $this;
        }
    }
    
    /**
     * Retrieve the joinUrl for attendees to join the session.
     * 
     * @param boolean $urlOnly
     * @param string $attendeeName unused
     * @param string $attendeeEmail unused
     * @param string $meetingPassword unused
     * @return \Smx\SimpleMeetings\JoinMe\Meeting|string
     */
    public function joinMeeting($urlOnly=false,$attendeeName=false,
            $attendeeEmail=false,$meetingPassword=false){
        $this->joinUrl = 'https://join.me/'.$this->meetingKey;
        if($urlOnly){
            return $this->joinUrl;
        } else {
            return $this;
        }
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @param array $options
     * @return boolean
     */
    public function editMeeting($options=false){
        return true;
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @return boolean
     */
    public function deleteMeeting(){
        return false;
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getActiveMeetings(){
        return new ItemList();
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @param array $options
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getRecordingList($options=false){
        return new ItemList();
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @param string $name
     * @param string $email
     * @param boolean $sendInvite
     * @return boolean
     */
    public function addAttendee($name, $email, $sendInvite=false){
        return false;
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getAttendeeList(){
        return new ItemList();
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getMeetingHistory(){
        return new ItemList();
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getAttendeeHistory(){
        return new ItemList();
    }
    
    /**
     * Dummy method to satisfy interface requirements.
     * 
     * @param type $timestamp
     * @return boolean
     */
    public function isValidTimestamp($timestamp){
        return true;
    }
    
    /**
     * Dummy function to satisfy interface
     * @return boolean
     */
    public function endMeeting()
    {
        return true;
    }
    
}