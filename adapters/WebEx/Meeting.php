<?php

namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Base\Meeting as MeetingBase;
use Smx\SimpleMeetings\Base\ItemList;
use Smx\SimpleMeetings\WebEx\Utilities;
use Zend\Http\Client;


class Meeting extends MeetingBase implements \Smx\SimpleMeetings\Meeting
{
    private $error = null;
    
    public function __construct($username, $password, $sitename, $options = false) {
        parent::__construct($username, $password, $sitename, $options);
    }
    
    /*
     * This method accepts an array of meeting settings. It calls the API
     * to schedule the meeting.
     * @param array $options If false, API will still be called with current properties
     * @return Meeting Returns $this on success
     * @throws ErrorException on API call failure
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
    /*
     * Method calls API to retrieve all meeting details.
     * This class only maintains the most common/necessary meeting details, 
     * so if you want to know every detail about the meeting use this method.
     * Results from this method will not be consistent across service providers.
     * 
     * @return SimpleXMLElement XML object for body content of API resposne
     */
    public function getServerMeetingDetails(){
        $xml = $this->loadXml('GetMeeting');
        $xml->body->bodyContent->meetingKey = $this->meetingKey;
        $result = $this->callApi($xml->asXML());
        return $result;
    }
    
    /*
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
        }
        return $meetingList;
    }
    
    /*
     * This method will generate the url for hosts to start the meeting.
     * @param boolean $urlOnly If true, the method will return a string with
     *  the url, if empty or false the url can be accessed via the hostUrl property.
     * @return Meeting|string The host url if $urlOnly=true or $this if false
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
    
    /*
     * This method will generate the url for attendees to join the meeting.
     * If attendeeName, attendeeEmail, and meetingPassword are included the 
     * url will put the user directly into the meeting without prompting for
     * the information. If using the parameters to generate multiple join urls, 
     * be sure to pass different information each time.
     * @param boolean $urlOnly If true, the method will return a string with
     *  the url, if empty or false the url can be accessed via the joinUrl property.
     * @param string $attendeeName If provided this will be included in the join url.
     * @param string $attendeeEmail If provided this will be included in the join url.
     * @param string $meetingPassword If provided this will be included in the join url.
     * @return Meeting|string The join url if $urlOnly=true or $this if false
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
    
    /*
     * This method accepts an array of meeting settings. It calls the API
     * to update the meeting.
     * @param array $options If false, API will still be called with current 
     *  properties in case they have been modified directly
     * @return Meeting Returns $this on success
     * @throws ErrorException on API call failure
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
    
    /*
     * Deletes this meeting from WebEx
     * @return Meeting Returns $this on success
     * @throws ErrorException on API call failure
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
    
    /*
     * This method will get a list of meetings currently in progress. If the
     * user making the API call is a site admin it will include all open meetings.
     * If the user making the API call is just a host it will only return their
     * open meetings.
     * @return MeetingList An iteratable list of meeting objects
     * @throws ErrorException on API call failure
     */
    public function getActiveMeetings(){
        
    }
    
    
    public function getRecordingList(){
        
    }
    public function addAttendee(){
        
    }
    public function getAttendeeList(){
        
    }
    public function getMeetingHistory(){
        
    }
    public function getAttendeeHistory(){
        
    }
    public function setOptions($options){
        
    }
    public function setOption($name,$value){
        
    }
    public function getOption($name){
        
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

