<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Citrix;
use Smx\SimpleMeetings\Shared\ItemList;
use Smx\SimpleMeetings\Citrix\Account;
use Smx\SimpleMeetings\Citrix\Utilities;
use Smx\SimpleMeetings\Citrix\Attendee;

/**
 * Citrix Meetings class. Adds functionality for calling Citrix REST APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Meeting extends Account implements \Smx\SimpleMeetings\Meeting
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
    public $telephonyType = 'Hybrid';
    public $telephonyInfo;
    public $meetingType = 'Scheduled';
    public $uniqueMeetingId;
    public $organizerKey;
    
    /**
     * History details is an array of the actual meeting usage data for a meeting
     * that has taken place. The expected fields are startTime, endTime, duration,
     * totalParticipants, totalPeopleMinutes, totalVoip, totalPhone
     */
    public $historyDetails = array();
    
    public function __construct($authInfo, $options = false) {
        parent::__construct($authInfo);
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        if(is_null($this->meetingName)){
            $this->meetingName = "My Meeting";
        }
        if(is_null($this->startTime)){
            $this->startTime = strtotime('+1 day 10:00:00');
        }
    }
    
    public function createMeeting($options=false){
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        
        if($this->isAuthenticated() && $this->isReady()){
            $url = 'https://api.citrixonline.com/G2M/rest/meetings';

            $startTime = date('c',$this->startTime);
            $endTime = date('c',$this->startTime + $this->duration*60);

            $data = array(
                'subject' => $this->meetingName,
                'starttime' => $startTime,
                'endtime' => $endTime,
                'passwordrequired' => 'false',
                'conferencecallinfo' => $this->telephonyType,
                'timezonekey' => '',
                'meetingtype' => $this->meetingType
            );

            $meeting = Utilities::callApi($url, $this->getAccessToken(), 'POST', $data);
            if($meeting){
                if(is_array($meeting)){
                    $meeting = $meeting[0];
                }
                $this->meetingKey = $meeting->meetingid;
                $this->joinUrl = $meeting->joinURL;
                $this->telephonyInfo = $meeting->conferenceCallInfo;
            }
        } else {
            if(!$this->isAuthenticated()){
                throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to schedule meetings.',170);
            } else {
                throw new \ErrorException('A meeting name and start time are
                     required.',171);
            }
        }
    }
    
    public function getServerMeetingDetails(){
        if($this->isAuthenticated()){
            if(isset($this->meetingKey)){
                $url = 'https://api.citrixonline.com/G2M/rest/meetings/'.$this->meetingKey;
                $meeting = Utilities::callApi($url, $this->getAccessToken(), 'GET');
                if(is_array($meeting)){
                    $meeting = $meeting[0];
                }
                return $meeting;
            } else {
                throw new \ErrorException('A meeting key must be set to retreive
                    meeting details.',173);
            }
        } else {
            throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to get meeting details.',172);
        }
    }
    
    public function getMeetingList($options=false){
        if($this->isAuthenticated()){
            if(!isset($options['startTime'])){
                $startTime = date('c',time()-2592000);
            }
            if(!isset($options['endTime'])){
                $endTime = date('c',time()+5184000);
            }

            $url = "https://api.citrixonline.com/G2M/rest/meetings".
                    "?scheduled=true&history=false&startDate=$startTime".
                    "&endDate=$endTime";
            $response = Utilities::callApi($url, $this->getAccessToken(), 'GET');
            $meetingList = new ItemList();
            if(is_array($response) && count($response) > 0){
                foreach($response as $meeting){
                    $startTime = strtotime($meeting->startTime);
                    $duration = strtotime($meeting->endTime) - $startTime;
                    $mtgDetails = array(
                        'meetingName' => $meeting->subject,
                        'startTime' => $startTime,
                        'duration' => $duration,
                        'meetingKey' => $meeting->meetingId,
                        'telephonyInfo' => $meeting->conferenceCallInfo,
                        'organizerKey' => $meeting->organizerKey
                    );
                    $meetingList->addItem(
                            new Meeting(
                                $this->getAuthInfo(),
                                $mtgDetails
                            )
                    );
                }
            }
            return $meetingList;
        } else {
            throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to get meeting list.',174);
        }
    }
    
    public function startMeeting($urlOnly=false){
        if($this->isAuthenticated() && isset($this->meetingKey)){
            $url = 'https://api.citrixonline.com/G2M/rest/meetings/'.
                $this->meetingKey.'/start';
            $response = Utilities::callApi($url, $this->getAccessToken(), 'GET');
            if(is_array($response)){
                $response = $response[0];
            }
            $this->hostUrl = $response->hostURL;
            if($urlOnly){
                return $this->hostUrl;
            } else {
                return $this;
            }
        } else {
            throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to start a meeting.',175);
        }
    }
    
    public function joinMeeting($urlOnly=false,$attendeeName=false,
            $attendeeEmail=false,$meetingPassword=false){
        if(isset($this->joinUrl)){
            if($urlOnly){
                return $this->joinUrl;
            } else {
                return $this;
            }
        } elseif(isset($this->meetingKey)){
            $this->joinUrl = 'https://www.gotomeeting.com/join/'.$this->meetingKey;
            if($urlOnly){
                return $this->joinUrl;
            } else {
                return $this;
            }
        } else {
            throw new \ErrorException('A meeting key is required to generate a 
                join url.',176);
        }
    }
    
    public function editMeeting($options=false){
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        
        if($this->isAuthenticated() && $this->isReady()){
            $startTime = date('c',$this->startTime);
            $endTime = date('c',$this->startTime + $this->duration*60);

            $data = array(
                'subject' => $this->meetingName,
                'starttime' => $startTime,
                'endtime' => $endTime,
                'passwordrequired' => 'false',
                'conferencecallinfo' => $this->telephonyType,
                'timezonekey' => '',
                'meetingtype' => $this->meetingType
            );
            
            $url = 'https://api.citrixonline.com/G2M/rest/meetings/'.$this->meetingKey;
            
            $response = Utilities::callApi($url, $this->getAccessToken(), 'PUT', $data);
            if($response){
                if($response->success){
                    $this->setOptions($data);
                    return $this;
                }
            }
        } else {
            if(!$this->isAuthenticated()){
                throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to edit meetings.',177);
            } else {
                throw new \ErrorException('A meeting name and start time are
                     required.',178);
            }
        }
    }
    
    public function deleteMeeting(){
        if($this->isAuthenticated() && isset($this->meetingKey)){
            $url = 'https://api.citrixonline.com/G2M/rest/meetings/'.$this->meetingKey;
            $response = Utilities::callApi($url, $this->getAccessToken(), 'DELETE', $data);
            if($response){
                if($response->success){
                    return $this;
                }
            }
        } else {
            if(!$this->isAuthenticated()){
                throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to delete meetings.',179);
            } else {
                throw new \ErrorException('A meeting key is required to 
                    delete meetings.',180);
            }
        }
    }
    
    /**
     * Unsupported method, returns an empty list
     * 
     * Although the Citrix APIs claim to support returning the status of meetings,
     * after testing it was not working, so this method just returns an empty 
     * list instead.
     * 
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getActiveMeetings(){
        return new ItemList();
    }
    
    /**
     * Unsupported method, returns an empty list
     * 
     * GoToMeeting recordings are stored locally on the host's computer, so
     * there is no way to get a list of recordings from the server.
     * 
     * @return \Smx\SimpleMeetings\Shared\ItemList
     */
    public function getRecordingList($options=false){
        return new ItemList();
    }
    
    /**
     * Unsupported method, returns false
     * 
     * GoToMeeting APIs do not provide the ability to add an attendee to a 
     * meeting so this method only returns false
     * 
     * @param string $name
     * @param string $email
     * @param boolean $sendInvite
     * @return boolean
     */
    public function addAttendee($name=false, $email=false, $sendInvite=false){
        return false;
    }
    
    public function getAttendeeList(){
        if(!is_null($this->meetingKey)){
            if(!isset($this->uniqueMeetingId)){
                $this->loadFromServer();
            }
            $options = array(
                'meetingKey' => $this->uniqueMeetingId,
                'meetingStart' => $this->startTime - 1800,
                'meetingEnd' => $this->startTime + $this->duration*60
            );
            $attendee = new Attendee($this->getAuthInfo(), $options);
            return $attendee->getAttendeeList();
        } else {
            $this->error = 'Meeting must be scheduled before you can pull a 
                list of attendees.';
            throw new \ErrorException($this->error, 112);
        }
    }
    
    /**
     * Retrieve meeting usage history and store in $this->historyDetails. 
     * 
     * If $onlyThisMeeting is true, only this one meeting will be returned. If 
     * it is false, a list of meetings will be returned ordered by start time.
     * 
     * @param boolean $onlyThisMeeting If true, the history call will only get
     *   history details for this meeting, if false, you can use $options
     *   to specify search criteria for multiple meetings. If false this method
     *   returns an ItemList of meetings.
     * @param array|false $options Search options when wanting to retrieve 
     *   history for more meetings than just this one. Valid options are: 
     *   startTimeRangeStart and startTimeRangeEnd
     * @return \Smx\SimpleMeetings\WebEx\Meeting|\Smx\SimpleMeetings\Base\ItemList 
     *   If $onlyThisMeeting is false, returns ItemList of meetings.
     * @throws \ErrorException In case of API failure
     */
    public function getMeetingHistory($onlyThisMeeting=true, $options=false){
        if($this->isAuthenticated()){
            if(!isset($options['startTimeRangeStart'])){
                $startTime = date('c',time()-2592000);
            }
            if(!isset($options['startTimeRangeEnd'])){
                $endTime = date('c',time()+5184000);
            }
        }
    }
    
    public function getAttendeeHistory(){
        
    }
    
    public function getLastError(){
        return $this->error;
    }
    
    public function loadFromServer($meetingKey=false){
        if($meetingKey){
            $this->meetingKey = $meetingKey;
        }
        
        $serverDetails = $this->getServerMeetingDetails();
        if($serverDetails){
            $this->meetingName = $serverDetails->subject;
            $this->startTime = strtotime($serverDetails->startTime);
            $this->duration = strtotime($serverDetails->endTime) - $this->startTime;
            $this->telephonyInfo = $serverDetails->conferenceCallInfo;
            $this->enforcePassword = $serverDetails->passwordRequired;
            $this->meetingType = $serverDetails->meetingType;
            $this->uniqueMeetingId = $serverDetails->uniqueMeetingId;
            return true;
        }
        
        return false;
    }
    
    private function isReady(){
        $ready = true;
        if(is_null($this->meetingName)){
            $ready = false;
            $this->error = "Meeting name is required";
        } elseif(is_null($this->startTime)){
            $ready = false;
            $this->error = "Start time is required";
        }
            
        return $ready;
    }
}
