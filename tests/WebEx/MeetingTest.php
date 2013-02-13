<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;

class MeetingTest extends \PHPUnit_Framework_TestCase
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
    
    public function testLoadXml()
    {
        $username = 'testuser';
        $password = 'testpass';
        $sitename = 'testsite';
        
        $meeting = Factory::SmxSimpleMeeting('WebEx','Meeting', $username, $password, $sitename);
        
        $xml = $meeting->loadXml('CreateMeeting');
        $this->assertInstanceOf('SimpleXmlElement', $xml);
    }
    
    public function testCreateMeetingWithDefaults()
    {
        
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        $this->assertRegExp('/[0-9]{1,}/', $meeting->meetingKey);
        
        return $meeting;
    }
    
    /*
     * @depends testCreateMeetingWithDefaults
     */
    public function testGetHostJoinUrls()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $hostUrl = $meeting->startMeeting(true);
        $this->assertStringStartsWith('http', $hostUrl);
        
        $genericJoinUrl = $meeting->joinMeeting(true);
        $this->assertStringStartsWith('http', $genericJoinUrl);
        
        $specificJoinUrl = $meeting->joinMeeting(true,'Phillip',
                'phillips@corp.sumilux.com','Sumi123');
        $this->assertStringStartsWith('http', $specificJoinUrl);
    }
    
    public function testEditMeeting()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $options = array(
            'meetingName' => __FUNCTION__.' - Has been edited!',
            'duration' => '15'
        );
        
        $this->assertNotEquals($options['meetingName'], $meeting->meetingName);
        
        $meeting->editMeeting($options);
        
        $srvMeeting = $meeting->getServerMeetingDetails();
        
        $this->assertEquals($options['meetingName'], $srvMeeting->metaData->confName->__toString());
    }
    
    public function testDeleteMeeting()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123', 'meetingName' => __FUNCTION__));
        
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Base\\Meeting', $meeting->deleteMeeting());
    }
    
    public function testGetMeetingList()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $list = $meeting->getMeetingList();
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Base\\ItemList', $list);
    }
    
    public function testGetActiveMeetings()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $list = $meeting->getActiveMeetings();
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Base\\ItemList', $list);
    }
    
    public function testGetRecordingList()
    {
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $list = $meeting->getRecordingList();
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Base\\ItemList', $list);
    }
    
    public function testGetMeetingHistory()
    {
        /*
         * Test getting a list of all meeting usage for past month
         */
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        
        // Get meetings for past month
        $searchOptions = array(
            'startTimeRangeStart' => date('m/d/Y 00:00:00',time()-2592000),
            'startTimeRangeEnd' => date('m/d/Y 00:00:00',time())
        );
        
        $list = $meeting->getMeetingHistory(false,$searchOptions);
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Base\\ItemList', $list);
        
        /*
         * Use one of the results to test pulling history for a single meeting
         */
        $sample = $list->current();
        
        $newMeeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $this->WebExUsername, $this->WebExPassword, $this->WebExSitename);
        $newMeeting->setOptions(array(
            'meetingKey' => $sample->meetingKey
        ));
        $newMeeting->getMeetingHistory();
        
        $this->assertGreaterThanOrEqual(7, count($newMeeting->historyDetails));
    }
}
