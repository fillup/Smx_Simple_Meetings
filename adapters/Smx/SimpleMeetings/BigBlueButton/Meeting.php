<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\BigBlueButton;
use Smx\SimpleMeetings\BigBlueButton\Account;
use Smx\SimpleMeetings\BigBlueButton\Utilities;
use Smx\SimpleMeetings\BigBlueButton\Attendee;
use Smx\SimpleMeetings\Shared\ItemList;

/**
 * BigBlueButton Meetings class to extend base meeting. Adds functionality for calling
 * BigBlueButton APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Meeting extends Account implements \Smx\SimpleMeetings\Interfaces\Meeting
{
    private $error = null;
    public $isPublic = false;
    public $enforcePassword = true;
    public $meetingPassword = null;
    public $meetingName = null;
    public $startTime = null;
    public $duration = 60;
    public $meetingKey = null;
    public $hostUrl = null;
    public $joinUrl = null;
    public $hostPassword = null;
    public $inProgress = false;
    public $recordingId = null;
    public $enableRecording = true;
    public $exitUrl = false;
    
    public function __construct($authInfo, $options=false) {
        parent::__construct($authInfo);
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        if(is_null($this->meetingName)){
            $this->meetingName = "My meeting";
        }
        if(is_null($this->startTime)){
            $this->startTime = time()+300;
        }
        if(is_null($this->meetingPassword)){
            $this->meetingPassword = mt_rand(100000, 999999);
        }
        if(is_null($this->hostPassword)){
            $this->hostPassword = mt_rand(100000, 999999);
        }
        if(is_null($this->meetingKey)){
            $this->meetingKey = mt_rand(100000, 999999);
        }
    }
    
    public function createMeeting($options=false){
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        
        $meetingOptions = array(
            'name' => $this->meetingName,
            'meetingID' => $this->meetingKey,
            'attendeePW' => $this->meetingPassword,
            'moderatorPW' => $this->hostPassword,
            'duration' => $this->duration,
            'record' => (string)$this->enableRecording
        );
        
        if($this->exitUrl){
            $meetingOptions['logoutURL'] = $this->exitUrl;
        }
        
        $request = Utilities::callApi($this->baseUrl, $meetingOptions, 
                'create', $this->salt);
        
        if($request){
            $this->startTime = $request->createTime->__toString();
        }
        
        return $this;
    }
    
    public function getServerMeetingDetails(){
        if(!is_null($this->hostPassword) && !is_null($this->meetingKey)){
            $queryParams = array(
                'meetingID' => $this->meetingKey,
                'password' => $this->hostPassword
            );

            $request = Utilities::callApi($this->baseUrl, $queryParams, 
                'getMeetingInfo', $this->salt);
            if($request){
                return $request;
            }
        } else {
            throw new \ErrorException('The meeting ID and moderator password
                are required to pull meeting information from the server.', 305);
        }
    }
    
    public function getMeetingList($options=false){
        
        $meetingList = new ItemList();
        
        $queryParams = array(
            'ignore' => 'param'
        );
        $request = Utilities::callApi($this->baseUrl, $queryParams, 
            'getMeetings', $this->salt);
        if($request){
            if($request->messageKey != 'noMeetings'){
                foreach($request->meetings->meeting as $meeting){
                    $meetingDetails = array(
                        'meetingKey' => $meeting->meetingID->__toString(),
                        'meetingName' => $meeting->meetingName->__toString(),
                        'startTime' => $meeting->createTime->__toString(),
                        'meetingPassword' => $meeting->attendeePW->__toString(),
                        'hostPassword' => $meeting->moderatorPW->__toString()
                    );
                    $inProgress = $meeting->running->__toString();
                    if($inProgress == 'true'){
                        $meetingDetails['inProgress'] = true;
                    }
                    $meetingList->addItem(new Meeting($this->getAuthInfo(),$meetingDetails));
                }
            }
        }
        return $meetingList;
    }
            
    public function startMeeting($urlOnly=false, $options=array()){
        $name = isset($options['name']) ? $options['name'] : 'Moderator';
        $userId = isset($options['userId']) ? $options['userId'] : false;
        
        $queryParams = array(
            'meetingID' => $this->meetingKey,
            'password' => $this->hostPassword,
            'fullName' => $name
        );
        if($userId){
            $queryParams['userID'] = $userId;
        }
        $this->hostUrl = Utilities::callApi($this->baseUrl, $queryParams, 
                'join', $this->salt, 'GET', null, true);
        if($urlOnly){
            return $this->hostUrl;
        } else {
            return $this;
        }
    }
    
    
    public function joinMeeting($urlOnly=false,$attendeeName=false,
            $attendeeEmail=false,$meetingPassword=false){
        $name = $attendeeName ? $attendeeName : 'Attendee';
        $queryParams = array(
            'meetingID' => $this->meetingKey,
            'fullName' => $name
        );
        if($meetingPassword){
            $queryParams['password'] = $meetingPassword;
        } elseif($this->meetingPassword){
            $queryParams['password'] = $this->meetingPassword;
        }
        $this->joinUrl = Utilities::callApi($this->baseUrl, $queryParams, 
                'join', $this->salt, 'GET', null, true);
        if($urlOnly){
            return $this->joinUrl;
        } else {
            return $this;
        }
    }
    
    
    public function editMeeting($options=false){
        return $this;
    }
    
    
    public function deleteMeeting(){
        return $this;
    }
    
    
    public function getActiveMeetings(){
        $activeMeetings = new ItemList();
        $allMeetings = $this->getMeetingList();
        foreach($allMeetings as $meeting){
            if($meeting->inProgress){
                $activeMeetings->addItem($meeting);
            }
        }
        return $activeMeetings;
    }
    
    
    public function getRecordingList($options=false){
        $recordingList = new ItemList();
        $request = Utilities::callApi($this->baseUrl, array('ignore' => 'param'), 
                'getRecordings', $this->salt);
        if($request){
            foreach($request->recordings->recording as $meeting){
                $meetingDetails = array(
                    'meetingKey' => $meeting->meetingID->__toString(),
                    'meetingName' => $meeting->name->__toString(),
                    'startTime' => $meeting->startTime->__toString(),
                    'meetingPassword' => $meeting->attendeePW->__toString(),
                    'hostPassword' => $meeting->moderatorPW->__toString(),
                    'recordingId' => $meeting->recordingID->__toString(),
                    'duration' => $meeting->playback->format->length->__toString(),
                    'joinUrl' => $meeting->playback->format->url->__toString()
                );
                if($meeting->published->__toString() == 'true'){
                    $meetingDetails['isPublic'] = true;
                }
                $recordingList->addItem(new Meeting($this->getAuthInfo(),$meetingDetails));
            }
        }
        return $recordingList;
    }
    
    
    public function addAttendee($name, $email, $sendInvite=false){
        return $this;
    }
    
    
    public function getAttendeeList(){
        $attendeeList = new ItemList();
        $details = $this->getServerMeetingDetails();
        if($details->attendees){
            foreach($details->attendees->attendee as $attendee){
                $attendeeDetails = new \stdClass();
                $attendeeDetails->name = $attendee->fullName->__toString();
                $attendeeDetails->userId = $attendee->userID->__toString();
                $attendeeDetails->role = $attendee->role->__toString();
                $attendeeDetails->meetingKey = $this->meetingKey;
                
                $attendeeList->addItem($attendeeDetails);
            }
        }
        return $attendeeList;
    }
    
    public function publishRecording()
    {
        if(!is_null($this->recordingId)){
            $key = $this->recordingId;
        } elseif(!is_null($this->meetingKey)){
            $key = $this->meetingKey;
        } else {
            throw new \ErrorException('A meeting ID or recording ID is required 
                to publish the recording.',306);
        }
        $queryParams = array('recordingID' => $key);
        $request = Utilities::callApi($this->baseUrl, $queryParams, 'publishRecordings', $this->salt);
        if($request){
            return true;
        }
    }
    
    public function getMeetingHistory(){
        return false;
    }
    
    
    public function getAttendeeHistory(){
        return false;
    }
    
    
    public function isValidTimestamp($timestamp){
        return true;
    }
    
    public function endMeeting(){
        if($this->meetingKey && $this->hostPassword){
            $queryParams = array(
                'meetingID' => $this->meetingKey,
                'password' => $this->hostPassword
            );
            $request = Utilities::callApi($this->baseUrl, $queryParams, 
                'end', $this->salt);
            if($request){
                if($request->messageKey->__toString() == 'sentEndMeetingRequest'){
                    return true;
                } else {
                    throw new \ErrorException('Unable to end meeting. Error: '.$request->message->__toString(),307);
                }
            }
        } else {
            throw new \ErrorException('A meeting key and host password are required to end a meeting.',308);
        }
    }

}