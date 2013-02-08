<?php
require_once dirname(__FILE__).'/../../vendor/autoload.php';
use Smx\SimpleMeetings\Meeting;

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
        
        $meeting = new Smx\SimpleMeetings\Meeting($username, $password, $sitename, $options);
        
        $this->assertEquals($options['isPublic'],$meeting->isPublic);
        $this->assertEquals($options['enforcePassword'],$meeting->enforcePassword);
        $this->assertEquals($options['meetingPassword'],$meeting->meetingPassword);
        $this->assertEquals($options['meetingName'],$meeting->meetingName);
        $this->assertEquals($options['startTime'],$meeting->startTime);
        $this->assertEquals($options['duration'],$meeting->duration);
        $this->assertEquals($options['username'],$meeting->username);
        $this->assertEquals($options['password'],$meeting->password);
        $this->assertEquals($options['sitename'],$meeting->sitename);
    }
    
    public function testConstructWithNoOptions()
    {
        $username = 'testuser';
        $password = 'testpass';
        $sitename = 'testsite';
        
        $meeting = new Smx\SimpleMeetings\Meeting($username, $password, $sitename);
        
        $this->assertNotNull($meeting->isPublic);
        $this->assertNotNull($meeting->enforcePassword);
        $this->assertNull($meeting->meetingPassword);
        $this->assertNotNull($meeting->meetingName);
        $this->assertNotNull($meeting->startTime);
        $this->assertNotNull($meeting->duration);
        $this->assertNotNull($meeting->sitename);
        $this->assertNull($meeting->meetingKey);
        $this->assertNotNull($meeting->username);
        $this->assertNotNull($meeting->password);
    }
}
