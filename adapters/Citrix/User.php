<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Citrix;
use Smx\SimpleMeetings\Shared\ItemList;
use Smx\SimpleMeetings\Citrix\Account;
use Smx\SimpleMeetings\Citrix\Utilities;

/**
 * Citrix User class. Adds functionality for calling Citrix REST APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class User extends Account implements \Smx\SimpleMeetings\User
{
    const ROLE_HOST = 'HOST';
    const ROLE_ADMIN = 'ADMIN';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    
    public $firstName = null;
    public $lastName = null;
    public $email = null;
    public $username = null;
    public $role = User::ROLE_HOST;
    public $password = null;
    public $userId = null;
    public $status = User::STATUS_ACTIVE;
    public $loginUrl = null;
    public $sendWelcomeEmail = false;
    
    public function __construct($authInfo, $options=false) {
        parent::__construct($authInfo);
        if(is_array($options)){
            foreach ($options as $option => $value){
                $this->$option = $value;
            }
        }
    }
    
    public function createUser($options=false){
        
    }
    
    public function editUser($options=false){
        
    }
    
    public function loginUser($urlOnly=false){
        
    }
    
    /**
     * Load informaton about a user from the server.
     * 
     * Citrix allows loading user information using either an organizerKey or 
     * an email address. You must be an Admin of a corporate account to load
     * information about any user other than yourself. 
     * 
     * @param string $username Ether an organizerKey or email address 
     */
    public function getServerUserDetails($username=false){
        if($this->isAuthenticated()){
            if(!$username && !isset($this->username)){
                throw new \ErrorException('A username must be set or provided 
                    to pull server details.',183);
            } elseif(isset($this->username)) {
                $username = $this->username;
            }
            if(strpos($username, '@') > 0){
                $url = 'https://api.citrixonline.com/G2M/rest/organizers?email='.$username;
            } else {
                $url = 'https://api.citrixonline.com/G2M/rest/organizers/'.$username;
            }
            
            $response = Utilities::callApi($url, $this->getAccessToken(), 'GET');
            if(is_array($response)){
                $response = $response[0];
            }
            return $response;
        }
    }
    
    public function getUserList($options=false){
        
    }
    
    public function deactivate($username=false){
        
    }
}
