<?php
namespace Smx\SimpleMeetings\Tests\BigBlueButton;

require_once __DIR__.'/../../vendor/autoload.php';

use Smx\SimpleMeetings\Factory;

class MeetingTest extends \PHPUnit_Framework_TestCase
{
    private $authInfo;
    
    public function setUp() {
        parent::setUp();
        if(is_null($this->authInfo)){
            include __DIR__.'/../../config.local.php';
            $this->authInfo = array(
                'baseUrl' => $BBBBaseUrl,
                'salt' => $BBBSalt
            );
        }
    }
    
    public function testCreateMeeting()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo);
        
        $meetingOptions = array(
            'meetingName' => __FUNCTION__
        );
        $meeting->createMeeting();
        $this->assertRegExp('/[0-9]{10}/', $meeting->startTime);
    }
    
    public function testGetServerMeetingDetails()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo);
        
        $meetingOptions = array(
            'meetingName' => __FUNCTION__
        );
        $meeting->createMeeting();
        $details = $meeting->getServerMeetingDetails();
        $this->assertInstanceOf('\\SimpleXMLElement', $details);
    }
    
    public function testGetMeetingList()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo);
        $meetingList = $meeting->getMeetingList();
        //print_r($meetingList);
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $meetingList);
    }
    
    public function testStartJoinMeeting()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo);
        
        $meetingOptions = array(
            'meetingName' => __FUNCTION__,
            'hostPassword' => 'adminpass',
            'meetingPassword' => 'attendeepass'
        );
        $meeting->createMeeting($meetingOptions);
        $meeting->startMeeting(false, array('name' => 'meeting host'));
        $meeting->joinMeeting(false, 'meeting attendee');
        //print_r($meeting->hostUrl);
        //print_r($meeting->joinUrl);
        $this->assertStringStartsWith('http', $meeting->hostUrl);
        $this->assertStringStartsWith('http', $meeting->joinUrl);
    }
    
    public function testGetActiveMeetings()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo);
        $meetingList = $meeting->getActiveMeetings();
        //print_r($meetingList);
        
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $meetingList);
    }
    
    public function testGetRecordingList()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo);
        $meetingList = $meeting->getRecordingList();
        //print_r($meetingList);
        
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $meetingList);
    }
    
    public function testGetAttendeeList()
    {
        $meeting = Factory::SmxSimpleMeeting('BigBlueButton', 'Meeting', $this->authInfo, 
                array('meetingKey' => '678788', 'hostPassword' => 'adminpass'));
        $attendeeList = $meeting->getAttendeeList();
        //print_r($attendeeList);
        
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $attendeeList);
    }
    
}
