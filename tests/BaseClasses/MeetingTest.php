<?php
require_once __DIR__.'/../../SmxSimpleMeetings.php';
use Smx\SimpleMeetings\Factory;

class MeetingTest extends PHPUnit_Framework_TestCase
{
    public function testConstructWithAllOptions()
    {
        $options = array(
            'isPublic' => false,
            'enforcePassword' => true,
            'meetingPassword' => 'Pass123',
            'meetingName' => 'Test Meeting Name',
            'startTime' => '01/01/2014 10:00:00',
            'duration' => 60,
            'sitename' => null,
            'meetingKey' => null
        );
        
        $username = 'testuser';
        $password = 'testpass';
        $sitename = 'testsite';
        
        $meeting = Factory::SmxSimpleMeeting('WebEx','Meeting', $username, $password, $sitename, $options);
        
        $this->assertEquals($options['isPublic'],$meeting->isPublic);
        $this->assertEquals($options['enforcePassword'],$meeting->enforcePassword);
        $this->assertEquals($options['meetingPassword'],$meeting->meetingPassword);
        $this->assertEquals($options['meetingName'],$meeting->meetingName);
        $this->assertEquals($options['startTime'],$meeting->startTime);
        $this->assertEquals($options['duration'],$meeting->duration);
        $this->assertEquals($username,$meeting->getUsername());
        $this->assertEquals($password,$meeting->getPassword());
        $this->assertEquals($sitename,$meeting->getSitename());
    }
    
    public function testConstructWithNoOptions()
    {
        $username = 'testuser';
        $password = 'testpass';
        $sitename = 'testsite';
        
        $meeting = new Smx\SimpleMeetings\WebEx\Meeting($username, $password, $sitename);
        
        $this->assertNotNull($meeting->isPublic);
        $this->assertNotNull($meeting->enforcePassword);
        $this->assertNull($meeting->meetingPassword);
        $this->assertNotNull($meeting->meetingName);
        $this->assertNotNull($meeting->startTime);
        $this->assertNotNull($meeting->duration);
        $this->assertNotNull($meeting->getSitename());
        $this->assertNull($meeting->meetingKey);
        $this->assertNotNull($meeting->getUsername());
        $this->assertNotNull($meeting->getPassword());
    }
}
