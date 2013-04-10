<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../vendor/autoload.php';

use Smx\SimpleMeetings\Factory;

class AccountTest extends \PHPUnit_Framework_TestCase
{
    private $authInfo;
    
    protected function setUp()
    {
        if(is_null($this->authInfo)){
            include __DIR__.'/../../config.local.php';
            $this->authInfo = array(
                'username' => $WebExUsername,
                'password' => $WebExPassword,
                'sitename' => $WebExSitename
            );
        }
    }
    
    public function testValidateCredentialsValid()
    {
        $account = Factory::SmxSimpleMeeting('WebEx', 'Account', $this->authInfo);
        $result = $account->validateCredentials();
        $this->assertTrue($result);
    }
    
    public function testValidateCredentialsInvalid()
    {
        $invalid = array(
            'sitename' => $this->authInfo['sitename'],
            'username' => 'user1@somedomain.com',
            'password' => 'alksdfjfkjslkjfd'
        );
        $account = Factory::SmxSimpleMeeting('WebEx', 'Account', $invalid);
        $result = $account->validateCredentials();
        $this->assertFalse($result);
    }
    
}