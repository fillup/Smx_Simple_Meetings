<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Shared\ItemList;
use Smx\SimpleMeetings\WebEx\Utilities;
use Smx\SimpleMeetings\WebEx\Account;

/**
 * WebEx Attendee class to represent a meeting attendee and provide functions
 * to add attendees to a meeting and retrieve lists of attendees.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Attendee extends Account implements \Smx\SimpleMeetings\Interfaces\Attendee
{
    public $name = null;
    public $email = null;
    public $meetingKey = null;
    public $attendeeId = null;
    
    /**
     * Add attendee(s) to a meeting or retrieve a list of attendees
     * 
     * @param Array $authInfo Array containg WebEx authentication details
     * @param array $options An array of options such as name, email, meetingKey
     */
    public function __construct($authInfo, $options = false)
    {
        parent::__construct($authInfo);
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
    }
    
    /**
     * Add a single attendee to a meeting.
     * 
     * This method requires that at least hte attendee email address and meeting
     * key are already set.
     * 
     * @return Attendee If successful, the $this->attendeeId property will be set
     * @throws ErrorException If missing required properties or there is an 
     *   error with the API call
     */
    public function addAttendee($options=false)
    {
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
        if($this->isValid()){
            $xml = $this->loadXml('CreateAttendee');
            if($xml){
                if(!is_null($this->name)){
                    $xml->body->bodyContent->person->name = $this->getName();
                }
                $xml->body->bodyContent->person->email = $this->getEmail();
                $xml->body->bodyContent->sessionKey = $this->getMeetingKey();
                $results = $this->callApi($xml->asXML());
                if($results){
                    $this->setAttendeeId($results->attendeeId->__toString());
                    return $this;
                }
            }
        } else {
            throw new \ErrorException('Attendee is missing either email address
                or meeting key.',110);
        }
    }
    
    /**
     * Retrieve a list of attendees
     * 
     * @return ItemList
     * @throws ErrorException If there is a problem with API call.
     */
    public function getAttendeeList()
    {
        $attendeeList = new ItemList();
        $xml = $this->loadXml('ListAttendees');
        if($xml){
            $xml->body->bodyContent->sessionKey = $this->getMeetingKey();
            try{
                $results = $this->callApi($xml->asXML());
                if($results){
                    if((int)$results->matchingRecords->returned->__toString() > 0){
                        foreach($results->attendee as $person){
                            $personDetails = array(
                                'name' => $person->person->name->__toString(),
                                'email' => $person->person->email->__toString(),
                                'meetingKey' => $this->getMeetingKey()
                            );
                            $attendeeList->addItem(
                                    new Attendee(
                                            $this->getAuthInfo(),
                                            $personDetails
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
        return $attendeeList;
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
    
    public function isValid()
    {
        if(!is_null($this->email) && !is_null($this->meetingKey)){
            return true;
        }
        return false;
    }
    
    private function loadXml($action){
        return Utilities::loadXml($action, $this->getUsername(), 
                $this->getPassword(), $this->getSitename());
    }
    
    private function callApi($xml){
        return Utilities::callApi($xml, $this->getSitename());
    }
}