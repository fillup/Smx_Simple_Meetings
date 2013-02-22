<?php
namespace Smx\SimpleMeetings\Tests\Citrix;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;

class MeetingTest extends \PHPUnit_Framework_TestCase
{
    private $authInfo;
    
    public function setUp() {
        parent::setUp();
        if(is_null($this->authInfo)){
            include __DIR__.'/../../config.local.php';
            $this->authInfo = array(
                'apiKey' => $CitrixAPIKey,
                'accessToken' => $CitrixAccessToken
            );
        }
    }
    
    public function testCreateMeeting()
    {
        $options = array(
            'meetingName' => __FUNCTION__
        );
        
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo, $options);
        
        $meeting->createMeeting();
        $this->assertNotEmpty($meeting->meetingKey);
    }
    
    public function testGetServerMeetingDetails()
    {
        $options = array(
            'meetingName' => __FUNCTION__
        );
        
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo, $options);
        
        $meeting->createMeeting();
        $details = $meeting->getServerMeetingDetails();
        $this->assertTrue(is_object($details));
    }
    
    public function testGetMeetingList()
    {
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo);
        $response = $meeting->getMeetingList();
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $response);
    }
    
    public function testStartMeeting()
    {
        $options = array(
            'meetingName' => __FUNCTION__
        );
        
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo, $options);
        
        $meeting->createMeeting();
        $hostUrl = $meeting->startMeeting(true);
        $this->assertStringStartsWith('http', $hostUrl);
    }
    
    public function testJoinMeeting()
    {
        $options = array(
            'meetingKey' => '123123123'
        );
        
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo, $options);
        
        $joinUrl = $meeting->joinMeeting(true);
        $this->assertStringStartsWith('http', $joinUrl);
    }
    
    public function testEditMeeting()
    {
        $options = array(
            'meetingKey' => '567099397',
            'meetingName' => 'EDITED-'.__FUNCTION__
        );
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo, $options);
        $meeting->editMeeting();
        $this->assertEquals($options['meetingName'],$meeting->meetingName);
    }
    
    public function testGetActiveMeetings()
    {
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo);
        $meetings = $meeting->getActiveMeetings();
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $meetings);
    }
    
    public function testGetAttendeeList()
    {
        $options = array('meetingKey' => '657883645');
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', 
                $this->authInfo,$options);
        $attendees = $meeting->getAttendeeList();
        $this->assertTrue(true);
    }
}