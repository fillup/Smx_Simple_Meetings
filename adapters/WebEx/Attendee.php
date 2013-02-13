<?php

namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Base\Attendee as AttendeeBase;
use Smx\SimpleMeetings\Base\ItemList;
use Smx\SimpleMeetings\WebEx\Utilities;
use Zend\Http\Client;

class Attendee extends AttendeeBase implements \Smx\SimpleMeetings\Attendee
{
    /*
     * Add attendee(s) to a meeting or retrieve a list of attendees
     * @param string $username Username for making API calls
     * @param string $password Password for making API calls
     * @param string $sitename Sitename for making API calls
     * @param array $options An array of options such as name, email, meetingKey
     */
    public function __contstruct($username, $password, $sitename, $options=false)
    {
        parent::__construct($username, $password, $sitename, $options);
    }
    
    /*
     * Add a single attendee to a meeting.
     * This method requires that at least hte attendee email address and meeting
     * key are already set.
     * @return Attendee If successful, the $this->attendeeId property will be set
     * @throws ErrorException If missing required properties or there is an 
     *   error with the API call
     */
    public function addAttendee()
    {
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
    
    /*
     * Retrieve a list of attendees
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
                                            $this->getUsername(),
                                            $this->getPassword(),
                                            $this->getSitename(),
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