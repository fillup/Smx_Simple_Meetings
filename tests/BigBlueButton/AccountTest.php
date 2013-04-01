<?php
namespace Smx\SimpleMeetings\Tests\BigBlueButton;

require_once __DIR__.'/../../vendor/autoload.php';

use Smx\SimpleMeetings\Factory;

class AccountTest extends \PHPUnit_Framework_TestCase
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
    
    public function testGetAuthUrl()
    {
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Account', $this->authInfo);
        
        $authUrl = $meeting->getAuthUrl();
        $this->assertStringStartsWith('http', $authUrl);
    }
    
}
