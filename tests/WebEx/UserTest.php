<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../vendor/autoload.php';

use Smx\SimpleMeetings\Factory;
use Smx\SimpleMeetings\WebEx\User;

class UserTest extends \PHPUnit_Framework_TestCase
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
    
    public function testLoginUser()
    {
        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->authInfo);
        
        $userInfo = array(
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => $this->authInfo['username']
        );
        
        $user->setOptions($userInfo);
        
        $loginUrl = $user->loginUser(true);
        $this->assertStringStartsWith('http', $loginUrl);
    }
    
    public function testGetServerUserDetails()
    {
        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->authInfo);
        
        $userInfo = array(
            'username' => $this->authInfo['username']
        );
        
        $user->setOptions($userInfo);
        
        $userDetails = $user->getServerUserDetails();
        $this->assertInstanceOf('\\SimpleXMLElement', $userDetails);
    }
    
    public function testGetUserList()
    {
        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->authInfo);
        $userList = $user->getUserList();
        $this->assertInstanceOf('\\Smx\\SimpleMeetings\\Shared\\ItemList', $userList);
    }
    
//    public function testCreateEditDeactivateUser()
//    {
//        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->authInfo);
//        
//        /**
//         * Create User
//         */
//        $num = rand(10, 999);
//        $email = "smxtestuser$num@nodomain1234.com";
//        echo $email;
//        $userInfo = array(
//            'firstName' => 'Test',
//            'lastName' => 'User',
//            'email' => $email,
//            'password' => 'TestPassword!',
//            'role' => User::ROLE_HOST
//        );
//
//        $user->createUser($userInfo);
//        $this->assertNotEmpty($user->userId);
//        
//        
//        /**
//         * Edit User
//         */
//        $newInfo = array(
//            'firstName' => 'NewTest',
//            'lastName' => 'NewUser',
//            'password' => 'NewPassword!',
//        );
//        
//        
//        $user->editUser($newInfo);
//        $userServerInfo = $user->getServerUserDetails();
//        $this->assertEquals($newInfo['firstName'], $userServerInfo->firstName->__toString());
//        
//        /**
//         * Deactivate User
//         */
//        $user->deactivate();
//        $userDeactivatedServerInfo = $user->getServerUserDetails();
//        $this->assertEquals('DEACTIVATED', $userDeactivatedServerInfo->active->__toString());
//    }
//    
//    public function testCreateDeactivateAdmin()
//    {
//        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->authInfo);
//        
//        $num = rand(10, 999);
//        
//        $userInfo = array(
//            'firstName' => 'Test',
//            'lastName' => 'User',
//            'email' => "smxtestuser$num@nodomain1234.com",
//            'password' => 'TestPassword!',
//            'role' => User::ROLE_ADMIN
//        );
//
//        $user->createUser($userInfo);
//        echo $user->userId;
//        $this->assertNotEmpty($user->userId);
//        
//        /**
//         * Deactivate User
//         */
//        $user->deactivate();
//        $userDeactivatedServerInfo = $user->getServerUserDetails();
//        $this->assertEquals('DEACTIVATED', $userDeactivatedServerInfo->active->__toString());
//    }
    
}
