<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Citrix;
use Smx\SimpleMeetings\Base\Meeting as MeetingBase;
use Smx\SimpleMeetings\Base\ItemList;

/**
 * Citrix Meetings class to extend base meeting. Adds functionality for calling
 * Citrix REST APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Meeting extends MeetingBase implements \Smx\SimpleMeetings\Meeting
{
    private $error = null;
    
    public function __construct($username, $password, $sitename, $options = false) {
        parent::__construct($username, $password, $sitename, $options);
    }
    
    public function createMeeting($options=false){
        
    }
    
    public function getServerMeetingDetails();
    public function getMeetingList($options=false);
    public function startMeeting($urlOnly=false);
    public function joinMeeting($urlOnly=false,$attendeeName=false,
            $attendeeEmail=false,$meetingPassword=false);
    public function editMeeting($options=false);
    public function deleteMeeting();
    public function getActiveMeetings();
    public function getRecordingList($options=false);
    public function addAttendee($name, $email, $sendInvite=false);
    public function getAttendeeList();
    public function getMeetingHistory();
    public function getAttendeeHistory();
    
    public function getLastError(){
        return $this->error;
    }
    
    private function isReady(){
        $ready = true;
        if(is_null($this->meetingName)){
            $ready = false;
            $this->error = "Meeting name is required";
        } elseif(is_null()){
            
        }
            
            
        return $ready;
    }
}
