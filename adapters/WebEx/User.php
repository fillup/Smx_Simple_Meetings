<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\WebEx;
use Smx\SimpleMeetings\Base\ItemList;
use Smx\SimpleMeetings\Base\User as UserBase;
use Smx\SimpleMeetings\WebEx\Utilities;

class User extends UserBase implements \Smx\SimpleMeetings\User
{
    
    public function __construct($username = false, $password = false, $sitename = false, $options=false) {
        parent::__construct($username, $password, $sitename, $options);
    }
    
    public function createUser($options=false)
    {
        if(is_array($options)){
            foreach($options as $option => $value){
                $this->$option = $value;
            }
        }
        if($this->isValid()){
            $xml = $this->loadXml('CreateUser');
            if($xml){
                $xml->body->bodyContent->firstName = $this->firstName;
                $xml->body->bodyContent->lastName = $this->lastName;
                $xml->body->bodyContent->email = $this->email;
                if(!$this->username){
                    $this->username = $this->email;
                }
                $xml->body->bodyContent->webExId = $this->username;
                $xml->body->bodyContent->password = $this->password;
                if($this->role == User::ROLE_ADMIN){
                    $xml->body->bodyContent->privilege->siteAdmin = true;
                }
                if($this->sendWelcomeEmail){
                    $xml->body->bodyContent->sendWelcome = true;
                }
                
                $results = $this->callApi($xml->asXML());
                if($results){
                    $this->userId = $results->userId->__toString();
                }
            }
        } else {
            throw new \ErrorException('A User requires a firstName, lastName,
                email, and password to be created.',120);
        }
    }
    
    public function editUser($options=false)
    {
        if(is_array($options)){
            foreach($options as $option => $value){
                $this->$option = $value;
            }
        }
        if($this->isValid()){
            $xml = $this->loadXml('EditUser');
            if($xml){
                $xml->body->bodyContent->firstName = $this->firstName;
                $xml->body->bodyContent->lastName = $this->lastName;
                $xml->body->bodyContent->email = $this->email;
                if(!$this->username){
                    $this->username = $this->email;
                }
                $xml->body->bodyContent->webExId = $this->username;
                if(!is_null($this->password)){
                    $xml->body->bodyContent->password = $this->password;
                }
                if($this->role == User::ROLE_ADMIN){
                    $xml->body->bodyContent->privilege->siteAdmin = true;
                }
                if($this->status == User::STATUS_ACTIVE){
                    $xml->body->bodyContent->active = 'ACTIVATED';
                } elseif($this->status == User::STATUS_INACTIVE){
                    $xml->body->bodyContent->active = 'DEACTIVATED';
                }
                
                $results = $this->callApi($xml->asXML());
                if($results){
                    return $this;
                }
            }
        } else {
            throw new \ErrorException('A User requires a firstName, lastName,
                and email to be updated.',121);
        }
    }
    
    public function loginUser($urlOnly=false)
    {
        $xml = $this->loadXml('LoginUser');
        if($xml){
            $xml->body->bodyContent->webExID = $this->getUsername();
            $results = $this->callApi($xml->asXML());
            if($results){
                $this->loginUrl = $results->userLoginURL->__toString();
                if($urlOnly){
                    return $this->loginUrl;
                } else {
                    return $this;
                }
            }
        }
    }
    
    public function getServerUserDetails($username=false)
    {
        if($username){
            $this->username = $username;
        }
        if(!is_null($this->username)){
            $xml = $this->loadXml('GetUser');
            if($xml){
                $xml->body->bodyContent->webExId = $this->username;
                $results = $this->callApi($xml->asXML());
                return $results;
            }
        }
    }
    
    public function deactivate($username=false)
    {
        if($username){
            $this->username = $username;
        }
        if(!is_null($this->username)){
            $xml = $this->loadXml('EditUser');
            if($xml){
                $xml->body->bodyContent->webExId = $this->username;
                $xml->body->bodyContent->active = 'DEACTIVATED';
                $results = $this->callApi($xml->asXML());
                $this->status = User::STATUS_INACTIVE;
                return $results;
            }
        }
    }
    
    public function getUserList($options=false)
    {
        $userList = new ItemList();
        $xml = $this->loadXml('ListUsers');
        if($xml){
            if(is_array($options)){
                if(isset($options['username'])){
                    $xml->body->bodyContent->webExId = $options['username'];
                }
                if(isset($options['email'])){
                    $xml->body->bodyContent->email = $options['email'];
                }
                if(isset($options['status'])){
                    if($options['status'] == User::STATUS_ACTIVE){
                        $xml->body->bodyContent->active = 'ACTIVATED';
                    } elseif($options['status'] == User::STATUS_INACTIVE){
                        $xml->body->bodyContent->active = 'DEACTIVATED';
                    }
                }
                if(isset($options['regDateStart']) && isset($options['regDateEnd'])){
                    $xml->body->bodyContent->dataScope->regDateStart = $options['regDateStart'];
                    $xml->body->bodyContent->dataScope->regDateEnd = $options['regDateEnd'];
                }
            }
            
            $results = $this->callApi($xml->asXML());
            foreach($results->user as $user){
                $userInfo = array(
                    'firstName' => $user->firstName->__toString(),
                    'lastName'  => $user->lastName->__toString(),
                    'email'     => $user->email->__toString(),
                    'username'  => $user->webExId->__toString()
                );
                if($user->active->__toString() == 'ACTIVATED'){
                    $userInfo['status'] = User::STATUS_ACTIVE;
                } elseif($user->active->__toString() == 'DEACTIVATED'){
                    $userInfo['status'] = User::STATUS_INACTIVE;
                }
                $userList->addItem(
                    new User($this->getUsername(), $this->getPassword(),
                        $this->getSitename(), $userInfo)
                );
            }
        }
        
        return $userList;
    }
    
    /**
     * Check if required fields are set
     * 
     * @return boolean
     */
    public function isValid()
    {
        if(is_null($this->firstName) || is_null($this->lastName) || 
                is_null($this->email) || is_null($this->password)){
            return false;
        } else {
            return true;
        }
    }
    
    private function loadXml($action){
        return Utilities::loadXml($action, $this->getUsername(), 
                $this->getPassword(), $this->getSitename());
    }
    
    private function callApi($xml){
        return Utilities::callApi($xml, $this->getSitename());
    }
}