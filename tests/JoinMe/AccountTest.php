<?php
namespace Smx\SimpleMeetings\Tests\JoinMe;

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
                'email' => $JoinMeEmail,
                'password' => $JoinMePassword
            );
        }
    }
    
    public function testRequestAuthCode()
    {
        $account = Factory::SmxSimpleMeeting('JoinMe', 'Account', $this->authInfo);
        $this->assertRegExp('/[0-9a-zA-z]{32}/', $account->getAuthCode());
    }
    
    public function testValidateCredentialsValid()
    {
        $account = Factory::SmxSimpleMeeting('JoinMe', 'Account', $this->authInfo);
        $this->assertTrue($account->validateCredentials());
    }
    
    public function testValidateCredentialsInvalid()
    {
        $invalid = array(
            'email' => 'user@somedomain.com',
            'password' => ';lakjdsfl;akjdgflkajdflkja'
        );
        try {
            $account = Factory::SmxSimpleMeeting('JoinMe', 'Account', $invalid);
            $result = true;
        } catch (\ErrorException $e) {
            $result = false;
        }
        $this->assertFalse($result);
    }
}
