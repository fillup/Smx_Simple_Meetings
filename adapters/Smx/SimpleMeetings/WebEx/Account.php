<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Shared\Account as AccountBase;

/**
 * 
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Account extends AccountBase implements \Smx\SimpleMeetings\Interfaces\Account
{
    private $username;
    private $password;
    private $sitename;
    
    public function __construct($authInfo) {
        $authInfo['authType'] = Account::AUTH_SHAREDSECRET;
        parent::__construct($authInfo);
        $this->setAuthInfo($authInfo);
    }
    
    public function setAuthInfo($authInfo) {
        parent::setAuthInfo($authInfo);
        $this->username = isset($authInfo['username']) ? 
                $authInfo['username'] : null;
        $this->password = isset($authInfo['password']) ?
                $authInfo['password'] : null;
        $this->sitename = isset($authInfo['sitename']) ?
                $authInfo['sitename'] : null;
    }
    
    public function getSitename() {
        return $this->sitename;
    }
    
    public function setSitename($sitename) {
        $this->sitename = $sitename;
    }
    
    public function getUsername(){
        return $this->username;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    /**
     * Check if necessary credentials are in place for making API calls
     * 
     * @return boolean
     */
    public function isAuthenticated()
    {
        if(isset($this->username) && isset($this->password) && isset($this->sitename)){
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Test that the username/password/sitename provided are valid
     * 
     * @return boolean
     */
    public function validateCredentials() 
    {
        if($this->isAuthenticated()){
            $user = \Smx\SimpleMeetings\Factory::SmxSimpleMeeting(
                    'WebEx', 'User', array(
                    'sitename' => $this->sitename,
                    'username' => $this->username,
                    'password' => $this->password
            ));
            try {
                $details = $user->getServerUserDetails();
                return true;
            } catch (\ErrorException $e){
                return false;
            }
        } else {
            return false;
        }
    }
}