<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Base\Meeting as MeetingBase;
use Smx\SimpleMeetings\Base\ItemList;
use Smx\SimpleMeetings\WebEx\Utilities;
use Smx\SimpleMeetings\WebEx\Attendee;

/**
 * WebEx Meetings class to extend base meeting. Adds functionality for calling
 * WebEx XML APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Meeting extends MeetingBase implements \Smx\SimpleMeetings\Meeting
{
    private $error = null;
    
    public function __construct($username, $password, $sitename, $options = false) {
        parent::__construct($username, $password, $sitename, $options);
    }
    
    /**
     * This method accepts an array of meeting settings. It calls the API
     * to schedule the meeting.
     * 
     * @param array $options If false, API will still be called with current properties
     * @return \Smx\SimpleMeetings\WebEx\Meeting
     * @throws \ErrorException on API call failure
     */
    public function createMeeting($options=false){
        if($options && is_array($options)){
            foreach($options as $option => $value){
                $this->$option = $value;
            }
        }
       
        $xml = $this->loadXml('CreateMeeting');
        $xml->body->bodyContent->metaData->confName = $this->meetingName;
        $xml->body->bodyContent->schedule->startDate = $this->startTime;
        $xml->body->bodyContent->schedule->duration = $this->duration;
        $xml->body->bodyContent->accessControl->isPublic = $this->isPublic;
        $xml->body->bodyContent->accessControl->enforcePassword = $this->enforcePassword;
        if(!is_null($this->meetingPassword)){
            $xml->body->bodyContent->accessControl->meetingPassword = $this->meetingPassword;
        }
        
        $result = $this->callApi($xml->asXML());
        if($result){
            $this->meetingKey = $result->meetingkey->__toString();
            return $this;
        }
        
    }
    
    /**
     * Method calls API to retrieve all meeting details.
     * 
     * This class only maintains the most common/necessary meeting details, 
     * so if you want to know every detail about the meeting use this method.
     * Results from this method will not be consistent across service providers.
     * 
     * @return \SimpleXMLElement XML object for body content of API resposne
     */
    public function getServerMeetingDetails(){
        $xml = $this->loadXml('GetMeeting');
        $xml->body->bodyContent->meetingKey = $this->meetingKey;
        $result = $this->callApi($xml->asXML());
        return $result;
    }
    
    /**
     * Retrieve a list of meetings
     * 
     * Method uses options to query API to return list of meetings based on
     * criteria such as all meetings for a given host or all meetings between
     * a date range. In order for the API to retern meetings for other hosts, 
     * $this->hostUsername and $this->hostPassword must be a site admin account,
     * otherwise when searching for a date range it will only return meetings 
     * scheduled by $this->hostUsername. Also if an option for searchUsername 
     * as been provided and it does not match $this->hostUsername, the query 
     * search will only work if $this->hostUsername is a site admin user.
     * 
     * @param Array $options Array containing options for searchUsername, startTime, endTime, startFrom, maximumNum
     * @return \Smx\SimpleMeetings\Base\MeetingList An iterator object of Meeting objects
     */
    public function getMeetingList($options=false){
        $meetingList = new ItemList();
        $xml = $this->loadXml('LstSummaryMeeting');
        if($xml){
            if($options){
                if($options['searchUsername']){
                    $xml->body->bodyContent->hostWebExID = $options['searchUsername'];
                }
                if($options['startTime']){
                    $xml->body->bodyContent->dateScope->startDateStart = $options['startTime'];
                }
                if($options['endTime']){
                    $xml->body->bodyContent->dateScope->startDateEnd = $options['endTime'];
                }
                if($options['startFrom']){
                    $xml->body->bodyContent->listControl->startFrom = $options['startFrom'];
                }
                if($options['maximumNum']){
                    $xml->body->bodyContent->listControl->maximumNum = $options['maximumNum'];
                }
            }
            try{
                $results = $this->callApi($xml->asXML());
                if($results){
                    if((int)$results->matchingRecords->returned->__toString() > 0){
                        foreach($results->meeting as $meet){
                            $mtgDetails = array(
                                'meetingKey' => $meet->meetingKey->__toString(),
                                'meetingName' => $meet->confName->__toString(),
                                'hostUsername' => $meet->hostWebExID->__toString(),
                                'startTime' => $meet->startDate->__toString(),
                                'duration' => $meet->duration->__toString(),
                                'sitename' => $this->getSitename()
                            );
                            if($meet->listStatus->__toString == 'PUBLIC'){
                                $mtgDetails['isPublic'] = true;
                            } else {
                                $mtgDetails['isPublic'] = false;
                            }
                            $meetingList->addItem(
                                    new Meeting(
                                            $this->getUsername(),
                                            $this->getPassword(),
                                            $this->getSitename(),
                                            $mtgDetails
                                    )
                            );
                        }
                    }
                }
            } catch (\ErrorException $e) {
                if(!preg_match('/000015/', $e->getMessage())){
                    throw $e;
                }
            }
        }
        return $meetingList;
    }
    
    /**
     * This method will generate the url for hosts to start the meeting.
     * 
     * @param boolean $urlOnly If true, the method will return a string with
     *  the url, if empty or false the url can be accessed via the hostUrl property.
     * @return \Smx\SimpleMeetings\WebEx\Meeting|String
     */
    public function startMeeting($urlOnly=false){
        $xml = $this->loadXml('GetHostUrlMeeting');
        if($xml && $this->meetingKey){
            $xml->body->bodyContent->sessionKey = $this->meetingKey;
            $result = $this->callApi($xml->asXML());
            if($result){
                $this->hostUrl = $result->hostMeetingURL->__toString();
                if($urlOnly){
                    return $this->hostUrl;
                }
            }
        }
        return $this;
    }
    
    /**
     * This method will generate the url for attendees to join the meeting.
     * 
     * If attendeeName, attendeeEmail, and meetingPassword are included the 
     * url will put the user directly into the meeting without prompting for
     * the information. If using the parameters to generate multiple join urls, 
     * be sure to pass different information each time.
     * 
     * @param boolean $urlOnly If true, the method will return a string with
     *  the url, if empty or false the url can be accessed via the joinUrl property.
     * @param string $attendeeName If provided this will be included in the join url.
     * @param string $attendeeEmail If provided this will be included in the join url.
     * @param string $meetingPassword If provided this will be included in the join url.
     * @return \Smx\SimpleMeetings\WebEx\Meeting|String
     */
    public function joinMeeting($urlOnly=false,$attendeeName=false,
            $attendeeEmail=false,$meetingPassword=false){
        $xml = $this->loadXml('GetJoinUrlMeeting');
        if($xml && $this->meetingKey){
            $xml->body->bodyContent->sessionKey = $this->meetingKey;
            if($attendeeName){
                $xml->body->bodyContent->attendeeName = $attendeeName;
            }
            if($attendeeEmail){
                $xml->body->bodyContent->attendeeEmail = $attendeeEmail;
            }
            if($meetingPassword){
                $xml->body->bodyContent->meetingPW = $meetingPassword;
            }
            $result = $this->callApi($xml->asXML());
            if($result){
                $this->joinUrl = $result->joinMeetingURL->__toString();
                if($urlOnly){
                    return $this->joinUrl;
                }
            }
        }
        return $this;
    }
    
    /**
     * This method accepts an array of meeting settings. It calls the API
     * to update the meeting.
     * 
     * @param array $options If false, API will still be called with current 
     *  properties in case they have been modified directly
     * @return \Smx\SimpleMeetings\WebEx\Meeting 
     * @throws \ErrorException on API call failure
     */
    public function editMeeting($options=false){
        if($options && is_array($options)){
            foreach($options as $option => $value){
                $this->$option = $value;
            }
        }
       
        $xml = $this->loadXml('EditMeeting');
        $xml->body->bodyContent->meetingkey = $this->meetingKey;
        $xml->body->bodyContent->metaData->confName = $this->meetingName;
        $xml->body->bodyContent->schedule->startDate = $this->startTime;
        $xml->body->bodyContent->schedule->duration = $this->duration;
        $xml->body->bodyContent->accessControl->isPublic = $this->isPublic;
        $xml->body->bodyContent->accessControl->enforcePassword = $this->enforcePassword;
        if(!is_null($this->meetingPassword)){
            $xml->body->bodyContent->accessControl->meetingPassword = $this->meetingPassword;
        }
        
        $result = $this->callApi($xml->asXML());
        if($result){
            return $this;
        }
    }
    
    /**
     * Deletes this meeting from WebEx
     * 
     * @return \Smx\SimpleMeetings\WebEx\Meeting
     * @throws \ErrorException on API call failure
     */
    public function deleteMeeting(){
        $xml = $this->loadXml('DeleteMeeting');
        if($xml){
            $xml->body->bodyContent->meetingKey = $this->meetingKey;
            $results = $this->callApi($xml->asXML());
            if($results){
                return $this;
            }
        }
    }
    
    /**
     * This method will get a list of meetings currently in progress. 
     * 
     * If the user making the API call is a site admin it will include all 
     * open meetings. If the user making the API call is just a host it will 
     * only return their open meetings.
     * 
     * @return \Smx\SimpleMeetings\Base\ItemList
     * @throws ErrorException on API call failure
     */
    public function getActiveMeetings(){
        $meetingList = new ItemList();
        $xml = $this->loadXml('ListOpenSessions');
        if($xml){
            try{
                $results = $this->callApi($xml->asXML());
                if($results){
                    if((int)$results->matchingRecords->returned->__toString() > 0){
                        foreach($results->services->sessions as $meet){
                            $mtgDetails = array(
                                'meetingKey' => $meet->sessionKey->__toString(),
                                'meetingName' => $meet->sessionName->__toString(),
                                'hostUsername' => $meet->hostWebExID->__toString(),
                                'startTime' => $meet->startDate->__toString(),
                                'sitename' => $this->getSitename()
                            );
                            if($meet->listStatus->__toString == 'PUBLIC'){
                                $mtgDetails['isPublic'] = true;
                            } else {
                                $mtgDetails['isPublic'] = false;
                            }
                            $meetingList->addItem(
                                    new Meeting(
                                            $this->getUsername(),
                                            $this->getPassword(),
                                            $this->getSitename(),
                                            $mtgDetails
                                    )
                            );
                        }
                    }
                }
            } catch (\ErrorException $e) {
                if(!preg_match('/000015/', $e->getMessage())){
                    throw $e;
                }
            }
        }
        return $meetingList;
    }
    
    /**
     * Retrieve a list of meeting recordings.
     * 
     * The returned list makes use of the hostUrl and joinUrl parameters to 
     * store the download link and the playback link respectively.
     * 
     * @param Array $options
     * @return \Smx\SimpleMeetings\Base\ItemList
     */
    public function getRecordingList($options=false){
        $recordingList = new ItemList();
        $xml = $this->loadXml('ListRecordings');
        if($xml){
            if($options){
                if($options['searchUsername']){
                    $xml->body->bodyContent->hostWebExID = $options['searchUsername'];
                }
                if($options['startTime']){
                    $xml->body->bodyContent->createTimeScope->createTimeStart = $options['startTime'];
                }
                if($options['endTime']){
                    $xml->body->bodyContent->createTimeScope->createTimeEnd = $options['endTime'];
                }
                if($options['startFrom']){
                    $xml->body->bodyContent->listControl->startFrom = $options['startFrom'];
                }
                if($options['maximumNum']){
                    $xml->body->bodyContent->listControl->maximumNum = $options['maximumNum'];
                }
                if($options['meetingKey']){
                    $xml->body->bodyContent->sessionKey = $options['meetingKey'];
                }
            }
            
            $results = $this->callApi($xml->asXML());
            if($results){
                if((int)$results->matchingRecords->returned->__toString() > 0){
                    foreach($results->recording as $meet){
                        $mtgDetails = array(
                            'meetingKey' => $meet->sessionKey->__toString(),
                            'meetingName' => $meet->name->__toString(),
                            'hostUsername' => $meet->hostWebExID->__toString(),
                            'startTime' => $meet->createTime->__toString(),
                            'sitename' => $this->getSitename(),
                            'hostUrl' => $meet->fileURL->__toString(),
                            'joinUrl' => $meet->streamURL->__toString()
                        );
                        if($meet->listing->__toString == 'PUBLIC'){
                            $mtgDetails['isPublic'] = true;
                        } else {
                            $mtgDetails['isPublic'] = false;
                        }
                        $recordingList->addItem(
                                new Meeting(
                                        $this->getUsername(),
                                        $this->getPassword(),
                                        $this->getSitename(),
                                        $mtgDetails
                                )
                        );
                    }
                }
            }
        }
        return $recordingList;
    }
    
    /**
     * Add an attendee to the meeting
     * 
     * @param string $name Attendee's name
     * @param string $email Attendee's email address
     * @param boolean $sendInvite Whether or not the API should send the 
     *   attendee an email invite
     * @return String $attendeeId
     * @throws \ErrorException Exception thrown if there is an API error
     */
    public function addAttendee($name, $email, $sendInvite=false){
        if(!is_null($this->meetingKey)){
            $attendee = new Attendee($this->getUsername(), $this->getPassword(),
                    $this->getSitename(), array(
                        'name' => $name,
                        'email' => $email,
                        'meetingKey' => $this->meetingKey
                    ));
            $attendee->addAttendee();
            return $attendee->getAttendeeId();
        } else {
            $this->error = 'Meeting must be scheduled before an 
                attendee can be added.';
            throw new \ErrorException($this->error, 111);
        }
    }
    
    /**
     * Return a list of all attendees that have been added to this meeting
     * 
     * @return ItemList Iteratable list of Attendee objects
     */
    public function getAttendeeList(){
        if(!is_null($this->meetingKey)){
            $attendee = new Attendee($this->getUsername(), $this->getPassword(),
                    $this->getSitename(), array(
                        'meetingKey' => $this->meetingKey
                    ));
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
     *   startTimeRangeStart, startTimeRangeEnd, hostUsername, startFrom, 
     *   maximumNum
     * @return \Smx\SimpleMeetings\WebEx\Meeting|\Smx\SimpleMeetings\Base\ItemList 
     *   If $onlyThisMeeting is false, returns ItemList of meetings.
     * @throws \ErrorException In case of API failure
     */
    public function getMeetingHistory($onlyThisMeeting=true, $options=false){
        $xml = $this->loadXml('GetMeetingHistory');
        if($xml){
            if($onlyThisMeeting){
                $xml->body->bodyContent->meetingKey = $this->meetingKey;
            }
            if(is_array($options)){
                if(isset($options['startTimeRangeStart'])){
                    $xml->body->bodyContent->startTimeScope
                            ->sessionStartTimeStart = $options['startTimeRangeStart'];
                }
                if(isset($options['startTimeRangeEnd'])){
                    $xml->body->bodyContent->startTimeScope
                            ->sessionStartTimeEnd = $options['startTimeRangeEnd'];
                }
                if(isset($options['hostUsername'])){
                    $xml->body->bodyContent->hostWebExID = $options['hostUsername'];
                }
                if(isset($options['startFrom'])){
                    $xml->body->bodyContent->listControl->startFrom = $options['startFrom'];
                }
                if(isset($options['maximumNum'])){
                    $xml->body->bodyContent->hostWebExID = $options['maximumNum'];
                }
            }
            $historyList = new ItemList();
            try{
                $results = $this->callApi($xml->asXML());
                if($results){
                    if($onlyThisMeeting){
                        $meet = $results->meetingUsageHistory;
                        $totalPhone = $meet->totalCallInMinutes->__toString() +
                                $meet->totalCallInTollfreeMinutes->__toString() +
                                $meet->totalCallOutDomestic->__toString() +
                                $meet->totalCallOutInternational->__toString();
                        $this->historyDetails = array(
                            'startTime' => $meet->meetingStartTime->__toString(), 
                            'endTime' => $meet->meetingEndTime->__toString(), 
                            'duration' => $meet->duration->__toString(),
                            'totalParticipants' => $meet->totalParticipants->__toString(), 
                            'totalPeopleMinutes' => $meet->totalPeopleMinutes->__toString(), 
                            'totalVoip' => $meet->totalVoipMinutes->__toString(), 
                            'totalPhone' => $totalPhone
                        );
                        return $this;
                    }
                    else {
                        foreach($results->meetingUsageHistory as $meet){
                            $totalPhone = $meet->totalCallInMinutes->__toString() +
                                $meet->totalCallInTollfreeMinutes->__toString() +
                                $meet->totalCallOutDomestic->__toString() +
                                $meet->totalCallOutInternational->__toString();
                            
                            $mtgDetails = array(
                                'meetingKey' => $meet->sessionKey->__toString(),
                                'meetingName' => $meet->confName->__toString(),
                                'hostUsername' => $meet->hostWebExID->__toString(),
                                'startTime' => $meet->meetingStartTime->__toString(),
                                'duration' => $meet->duration->__toString(),
                                'sitename' => $this->getSitename()
                            );
                                                        
                            $historyDetails = array(
                                'startTime' => $meet->meetingStartTime->__toString(), 
                                'endTime' => $meet->meetingEndTime->__toString(), 
                                'duration' => $meet->duration->__toString(),
                                'totalParticipants' => $meet->totalParticipants->__toString(), 
                                'totalPeopleMinutes' => $meet->totalPeopleMinutes->__toString(), 
                                'totalVoip' => $meet->totalVoipMinutes->__toString(), 
                                'totalPhone' => $totalPhone
                            );
                            
                            $newMeeting = new Meeting($this->getUsername(),
                                    $this->getPassword(), $this->getSitename(),
                                    $mtgDetails);
                            $newMeeting->historyDetails = $historyDetails;
                            $historyList->addItem($newMeeting);
                        }
                        
                        
                        return $historyList;
                    }
                }
            } catch (\ErrorException $e) {
                if(!preg_match('/000015/', $e->getMessage())){
                    throw $e;
                } elseif(!$onlyThisMeeting) {
                    return $historyList;
                }
            }
        }
        return $this;
    }
    
    /**
     * Retrieve meeting attendee history and store in $this->attendeeHistoryDetails. 
     * 
     * If $onlyThisMeeting is true, only this one meeting will be returned. If it
     * is false, a list of meetings will be returned ordered by start time.
     * 
     * @param boolean $onlyThisMeeting If true, the history call will only get
     *   history details for this meeting, if false, you can use $options
     *   to specify search criteria for multiple meetings. If false this method
     *   returns an ItemList of meetings.
     * @param array|false $options Search options when wanting to retrieve 
     *   history for more meetings than just this one. Valid options are: 
     *   startTimeRangeStart, startTimeRangeEnd, hostUsername, startFrom, 
     *   maximumNum
     * @return \Smx\SimpleMeetings\WebEx\Meeting|\Smx\SimpleMeetings\Base\ItemList 
     *   If $onlyThisMeeting is false, returns ItemList of meetings.
     * @throws \ErrorException In case of API failure
     */
    public function getAttendeeHistory($onlyThisMeeting=true, $options=false){
        $xml = $this->loadXml('GetMeetingAttendeeHistory');
        if($xml){
            if($onlyThisMeeting){
                $xml->body->bodyContent->meetingKey = $this->meetingKey;
            }
            if(is_array($options)){
                if(isset($options['startTimeRangeStart'])){
                    $xml->body->bodyContent->startTimeScope
                            ->sessionStartTimeStart = $options['startTimeRangeStart'];
                }
                if(isset($options['startTimeRangeEnd'])){
                    $xml->body->bodyContent->startTimeScope
                            ->sessionStartTimeEnd = $options['startTimeRangeEnd'];
                }
                if(isset($options['hostUsername'])){
                    $xml->body->bodyContent->hostWebExID = $options['hostUsername'];
                }
                if(isset($options['startFrom'])){
                    $xml->body->bodyContent->listControl->startFrom = $options['startFrom'];
                }
                if(isset($options['maximumNum'])){
                    $xml->body->bodyContent->hostWebExID = $options['maximumNum'];
                }
            }
            $historyList = new ItemList();
            try{
                $results = $this->callApi($xml->asXML());
                if($results){
                    if($onlyThisMeeting){
                        $meet = $results->meetingAttendeeHistory;
                        $this->attendeeHistoryDetails = array(
                            'joinTime' => $meet->joinTime->__toString(), 
                            'leaveTime' => $meet->leaveTime->__toString(), 
                            'duration' => $meet->duration->__toString(),
                            'name' => $meet->name->__toString(), 
                            'email' => $meet->email->__toString(), 
                            'ipAddress' => $meet->ipAddress->__toString(),
                            'voipDuration' => $meet->voipDuration->__toString()
                        );
                        return $this;
                    } else {
                        foreach($results->meetingAttendeeHistory as $meet){
                            $mtgDetails = array(
                                'meetingKey' => $meet->meetingKey->__toString(),
                                'meetingName' => $meet->confName->__toString(),
                                'startTime' => $meet->joinTime->__toString(),
                                'duration' => $meet->duration->__toString(),
                                'sitename' => $this->getSitename()
                            );
                                                        
                            $attendeeDetails = array(
                                'joinTime' => $meet->joinTime->__toString(), 
                                'leaveTime' => $meet->leaveTime->__toString(), 
                                'duration' => $meet->duration->__toString(),
                                'name' => $meet->name->__toString(), 
                                'email' => $meet->email->__toString(), 
                                'ipAddress' => $meet->ipAddress->__toString(),
                                'voipDuration' => $meet->voipDuration->__toString()
                            );
                            
                            $newMeeting = new Meeting($this->getUsername(),
                                    $this->getPassword(), $this->getSitename(),
                                    $mtgDetails);
                            $newMeeting->attendeeHistoryDetails = $attendeeDetails;
                            $historyList->addItem($newMeeting);
                        }
                        
                        
                        return $historyList;
                    }
                }
            } catch (\ErrorException $e) {
                if(!preg_match('/000015/', $e->getMessage())){
                    throw $e;
                } elseif(!$onlyThisMeeting) {
                    return $historyList;
                }
            }
        }
        
        return $this;
    }
    
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
    
    public function loadXml($action){
        return Utilities::loadXml($action, $this->getUsername(), 
                $this->getPassword(), $this->getSitename());
    }
    
    public function callApi($xml){
        return Utilities::callApi($xml, $this->getSitename());
    }
}

