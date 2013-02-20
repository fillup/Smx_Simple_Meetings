<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\Base;

/**
 * 
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Account
{
    const AUTH_OAUTH = 'OAUTH';
    const AUTH_SHAREDSECRET = 'SHAREDSECRET';
    
    private $authInfo;
    private $authType;
    
    public function __construct($authInfo) {
        $this->setAuthInfo($authInfo);
    }
    
    public function getAuthType()
    {
        return $this->authType;
    }
    
    public function setAuthType($authType)
    {
        if(in_array($authType, array(
            Account::AUTH_OAUTH,  
            Account::AUTH_SHAREDSECRET
          ))){
            $this->authType = $authType;
        } else {
            throw new \ErrorException('Invalid authType',150);
        }
    }
    
    public function getAuthInfo()
    {
        return $this->authInfo;
    }
    
    public function setAuthInfo($authInfo){
        if(is_array($authInfo)){
            $this->authInfo = $authInfo;
            if(isset($authInfo['authType'])){
                $this->setAuthType($authInfo['authType']);
            }
        } else {
            throw new \ErrorException('AuthInfo must be an array.',151);
        }
    }
    
    public function setOptions($options){
        if($options && is_array($options)){
            foreach($options as $name => $value){
                $this->$name = $value;
            }
        }
    }
    
    public function setOption($name,$value){
        $this->$name = $value;
    }
    
    public function getOption($name){
        if(isset($this->$name)){
            return $this->$name;
        } else {
            throw new \UnexpectedValueException(
                    "Property named '$name' not current set on object.",
                    100
                    );
        }
    }
}
