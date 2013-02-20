<?php
namespace Smx\SimpleMeetings\Tests\WebEx;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;
use Smx\SimpleMeetings\Base\User as BaseUser;

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
    
//    public function testCreateEditDeactivateUser()
//    {
//        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->WebExUsername, 
//                $this->WebExPassword, $this->WebExSitename);
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
//            'role' => BaseUser::ROLE_HOST
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
//        $user = Factory::SmxSimpleMeeting('WebEx', 'User', $this->WebExUsername, 
//                $this->WebExPassword, $this->WebExSitename);
//        
//        $num = rand(10, 999);
//        
//        $userInfo = array(
//            'firstName' => 'Test',
//            'lastName' => 'User',
//            'email' => "smxtestuser$num@nodomain1234.com",
//            'password' => 'TestPassword!',
//            'role' => BaseUser::ROLE_ADMIN
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
