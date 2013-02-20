<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Base\Account as AccountBase;

/**
 * 
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Account extends AccountBase implements \Smx\SimpleMeetings\Account
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
}