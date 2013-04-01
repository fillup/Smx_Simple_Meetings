<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../vendor/autoload.php';

use Smx\SimpleMeetings\Factory;

class AttendeeTest extends \PHPUnit_Framework_TestCase
{
    private $authInfo;
    
    protected function setUp()
    {
        if(is_null($this->authInfo)){
            include __DIR__.'/../../config.local.php';
            $this->authInfo = array(
                'username' => $WebExUsername,
                'password' => $WebExPassword,
                'sitename' => $WebExSitename
            );
        }
    }
    
    public function testAddAttendee()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', $this->authInfo);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $attendeeInfo = array(
            'name' => 'Phillip',
            'email' => 'phillips@corp.sumilux.com',
            'meetingKey' => $meeting->meetingKey
        );
        
        $attendee = Factory::SmxSimpleMeeting('WebEx', 'Attendee', 
                $this->authInfo, $attendeeInfo);
        
        $attendee->addAttendee();
        $this->assertTrue(is_numeric($attendee->getAttendeeId()));
    }
    
    public function testGetAttendeeList()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->authInfo);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $attendeeInfo = array(
            'name' => 'Phillip',
            'email' => 'phillips@corp.sumilux.com',
            'meetingKey' => $meeting->meetingKey
        );
        
        $attendee = Factory::SmxSimpleMeeting('WebEx', 'Attendee', 
                $this->authInfo,
                $attendeeInfo);
        
        $attendee->addAttendee();
        
        //$attendeeId = $meeting->addAttendee('Phillip','phillips@corp.sumilus.com');
        
        $attendeeList = $attendee->getAttendeeList();
    }
    
}