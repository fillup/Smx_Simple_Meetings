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
    
    public function testValidateCredentialsValid()
    {
        $account = Factory::SmxSimpleMeeting('BigBlueButton', 'Account', $this->authInfo);
        $this->assertTrue($account->validateCredentials());
    }
    
    public function testValidateCredentialsInvalid()
    {
        $invalidSalt = array(
            'baseUrl' => $this->authInfo['baseUrl'].'asdf',
            'salt' => ';lakjdsfl;akjdgflkajdflkja'
        );
        $account = Factory::SmxSimpleMeeting('BigBlueButton', 'Account', $invalidSalt);
        $result = $account->validateCredentials();
        $this->assertFalse($result);
    }
    
}
