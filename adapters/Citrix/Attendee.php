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
use Smx\SimpleMeetings\Citrix\Utilities;
use Smx\SimpleMeetings\Citrix\Account;

/**
 * Citrix Attendee class to represent a meeting attendee. Unfortunately Citrix
 * APIs do not provide the ability to create/add attendees to a meeting but
 * we can list them.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Attendee extends Account implements \Smx\SimpleMeetings\Attendee
{
    public $name = null;
    public $email = null;
    public $meetingKey = null;
    public $attendeeId = null;
    public $meetingStart;
    public $meetingEnd;
    
    /**
     * Create Attendee object
     * 
     * @param Array $authInfo Array containg authentication details
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
     * Unsupported method, returns false
     * 
     * GoToMeeting APIs do not provide the ability to add an attendee to a 
     * meeting so this method only returns false
     * 
     * @param array $options
     * @return boolean
     */
    public function addAttendee($options=false)
    {
        return false;
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
        if($this->isAuthenticated() && isset($this->meetingKey)){
            $url = 'https://api.citrixonline.com/G2M/rest/meetings/'.
                    $this->meetingKey.'/attendees'.
                    '?startDate='.date('c',$this->meetingStart).
                    '&endDate='.date('c',$this->meetingEnd);
            $results = Utilities::callApi($url, $this->getAccessToken(), 'GET');
            if($results){
                foreach($results as $person){
                    $personDetails = array(
                        'name' => $person->attendeeName,
                        'email' => $person->attendeeEmail,
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
        } else {
            if(!$this->isAuthenticated()){
                throw new \ErrorException('User must be authenticated and an 
                    accessToken is needed to list attendees.',181);
            } else {
                throw new \ErrorException('A meeting key is required to 
                    list attendees.',182);
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
    
}