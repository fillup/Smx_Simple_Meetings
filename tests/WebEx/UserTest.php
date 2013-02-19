<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;

class UserTest extends \PHPUnit_Framework_TestCase
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
    
    public function testLoginUser()
    {
        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->WebExUsername, 
                $this->WebExPassword, $this->WebExSitename);
        
        $userInfo = array(
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => $this->WebExUsername
        );
        
        $user->setOptions($userInfo);
        
        $loginUrl = $user->loginUser(true);
        $this->assertStringStartsWith('http', $loginUrl);
    }
    
    public function testGetServerUserDetails()
    {
        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->WebExUsername, 
                $this->WebExPassword, $this->WebExSitename);
        
        $userInfo = array(
            'username' => $this->WebExUsername
        );
        
        $user->setOptions($userInfo);
        
        $userDetails = $user->getServerUserDetails();
        $this->assertInstanceOf('\\SimpleXMLElement', $userDetails);
    }
    
    public function testGetUserList()
    {
        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->WebExUsername, 
                $this->WebExPassword, $this->WebExSitename);
        $userList = $user->getUserList();
        print_r($userList);
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Base\\ItemList', $userList);
    }
}
