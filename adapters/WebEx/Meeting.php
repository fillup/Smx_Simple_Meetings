<?php

namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Base\Meeting as MeetingBase;
use Smx\SimpleMeetings\MeetingList;
use Zend\Http\Client;


class Meeting extends MeetingBase implements \Smx\SimpleMeetings\Meeting
{
    private $error = null;
    
    public function __construct($username, $password, $sitename, $options = false) {
        parent::__construct($username, $password, $sitename, $options);
    }
    
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
            $xml->body->bodyContent->accessControl->meetingPassword = $meetingPassword;
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
     * @return \Smx\SimpleMeetings\MeetingList An iterator object of Meeting objects
     */
    public function getMeetingList($options=false){
        $meetingList = new MeetingList();
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
                            'sitename' => $this->sitename
                        );
                        if($meet->listStatus->__toString == 'PUBLIC'){
                            $mtgDetails['isPublic'] = true;
                        } else {
                            $mtgDetails['isPublic'] = false;
                        }
                        $meetingList->addMeeting(
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
    public function startMeeting($urlOnly=false){
        
    }
    public function joinMeeting($urlOnly=false){
        
    }
    public function editMeeting(){
        
    }
    public function deleteMeeting(){
        
    }
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
    
    /*
     * Method to load XML template file, create XML object, inject user credentials
     * and return a SimpleXMLElement object for further alterations.
     * @param $action Should be a valid action name mapping to an XML file
     * @return SimpleXMLElement
     * @throws ErrorException Exception thrown when unable to load or parse the XML file.
     */
    private function loadXml($action){
        libxml_use_internal_errors(true);
        $file = '.xml/'.$action.'.xml';
        $xml = new \SimpleXMLElement($file);
        if($xml){
            $xml->header->securityContext->webExID = $this->getUsername();
            $xml->header->securityContext->password = $this->getPassword();
            $xml->header->securityContext->siteName = $this->getSitename();
            return $xml;
        } else {
            $errors = '';
            foreach(libxml_get_errors() as $error) {
                $errors .= ",\t" . $error->message;
            }
            $this->error = "Unable to load file $file, errors occured: $errors";
            throw new \ErrorException($this->error,105);
        }
    }
    
    /*
     * Performs API call to WebEx XML API
     * @param String|SimpleXMLElement $xml Either the full XML to be sent or a SimpleXMLElement object
     * @return SimpleXMLElement The bodyContent section of successful API response
     * @throws ErrorException when the API call fails or it is unable to parse response.
     */
    private function callApi($xml){
        if(is_object($xml)){
            $xml = $xml->asXML();
        }
        
        $url = "https://".$this->sitename.".webex.com/WBXService/XMLService";
        
        $client = new Client();
        $client->setUri($url);
        $client->setMethod('POST');
        $client->setRawBody($xml);
        
        $response = $client->send();
        if($response && $response->getStatusCode() == '200'){
            $body = $response->getBody();
            /*
             * Clear out unnecessary namespaces from returned XML
             */
            $body = preg_replace('/serv\:/', '', $body);
            $body = preg_replace('/use\:/', '', $body);
            $body = preg_replace('/com\:/', '', $body);
            $body = preg_replace('/meet\:/', '', $body);
            $body = preg_replace('/ep\:/', '', $body);
            
            libxml_use_internal_errors(true);
            $results = new \SimpleXMLElement($body);
            if($results){
                if($results->header->response->result == 'SUCCESS'){
                    return $results->body->bodyContent;
                } else {
                    $this->error = "An error was returned from the API: ".
                            $results->header->response->reason->__toString().' ('.
                            $results->header->response->exceptionID->__toString().')';
                    throw new \ErrorException($this->error,108);
                }
            } else {
                $errors = '';
                foreach(libxml_get_errors() as $error) {
                    $errors .= ",\t" . $error->message;
                }
                $this->error = "Unable to parse XML response, errors occured: $errors";
                throw new \ErrorException($this->error,106);
            }
        } else {
            $this->error = "API call returned an HTTP status code of ".$response->getStatusCode();
            throw new \ErrorException($this->error,107);
        }
    }
}

