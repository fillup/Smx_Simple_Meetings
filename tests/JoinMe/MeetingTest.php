<?php
namespace Smx\SimpleMeetings\Tests\JoinMe;

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
                'email' => $JoinMeEmail,
                'password' => $JoinMePassword
            );
        }
    }
    
    public function testCreateMeeting()
    {
        $meeting = Factory::SmxSimpleMeeting('JoinMe', 'Meeting', $this->authInfo);
        $meeting->createMeeting();
        $this->assertRegExp('/^[0-9]{9}$/', $meeting->meetingKey);
        $this->assertRegExp('/^[0-9]{9}$/', $meeting->meetingPassword);
        $this->assertStringStartsWith('http', $meeting->joinUrl);
        $this->assertStringStartsWith('http', $meeting->hostUrl);
    }
}