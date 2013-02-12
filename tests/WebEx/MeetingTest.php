<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;

class MeetingTest extends \PHPUnit_Framework_TestCase
{
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
        require_once __DIR__.'/../../config.local.php';
        
        $meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', $WebExUsername, $WebExPassword, $WebExSitename);
        $meeting->createMeeting(array('meetingPassword'=>'Sumi123'));
        $this->assertRegExp('/[0-9]{1,}/', $meeting->meetingKey);
    }
}
