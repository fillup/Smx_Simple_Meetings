<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;

class AttendeeTest extends \PHPUnit_Framework_TestCase
{
    
    private $WebExUsername;
    private $WebExPassword;
    private $WebExSitename;
    
    protected function setUp()
    {
        if(is_null($this->WebExUsername)){
            include __DIR__.'/../../config.local.php';
            $this->WebExUsername = $WebExUsername;
            $this->WebExPassword = $WebExPassword;
            $this->WebExSitename = $WebExSitename;
        }
    }
    
    public function testAddAttendee()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $attendeeInfo = array(
            'name' => 'Phillip',
            'email' => 'phillips@corp.sumilux.com',
            'meetingKey' => $meeting->meetingKey
        );
        
        $attendee = Factory::SmxSimpleMeeting('WebEx', 'Attendee', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename,
                $attendeeInfo);
        
        $attendee->addAttendee();
        
        $this->assertTrue(is_numeric($attendee->getAttendeeId()));
    }
    
    public function testGetAttendeeList()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $attendeeInfo = array(
            'name' => 'Phillip',
            'email' => 'phillips@corp.sumilux.com',
            'meetingKey' => $meeting->meetingKey
        );
        
        $attendee = Factory::SmxSimpleMeeting('WebEx', 'Attendee', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename,
                $attendeeInfo);
        
        $attendee->addAttendee();
        
        //$attendeeId = $meeting->addAttendee('Phillip','phillips@corp.sumilus.com');
        
        $attendeeList = $attendee->getAttendeeList();
    }
    
}