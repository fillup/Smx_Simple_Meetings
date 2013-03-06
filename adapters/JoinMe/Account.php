<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\JoinMe;
use Smx\SimpleMeetings\Base\Account as AccountBase;
use Smx\SimpleMeetings\JoinMe\Utilities;

/**
 * Account class acts as base class for Meetings in order to handle authentication
 * information
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Account extends AccountBase implements \Smx\SimpleMeetings\Account
{
    private $email = null;
    private $password = null;
    private $authCode = null;
    
    /**
     * Provide either an email address and password, or an authCode.
     * 
     * @param array $authInfo
     */
    public function __construct($authInfo)
    {
        $authInfo['authType'] = Account::AUTH_SHAREDSECRET;
        parent::__construct($authInfo);
        $this->setAuthInfo($authInfo);
    }
    
    /**
     * Set internal auth information. If authCode is not provided, one will be
     * requested from the JoinMe API. The authCode can be reused and is valid 
     * until another one is requested, so you may persist it somewhere rather 
     * than the user's actual password. However, the JoinMe API does not have 
     * a concept of an application token as well, so if the user uses another 
     * service to access their account it will invalidate the authcode you have
     * stored.
     * 
     * @param array $authInfo
     */
    public function setAuthInfo($authInfo) {
        parent::setAuthInfo($authInfo);
        $this->email = isset($authInfo['email']) ? $authInfo['email'] : null;
        $this->password = isset($authInfo['password']) ? $authInfo['password'] : null;
        $this->authCode = isset($authInfo['authCode']) ? $authInfo['authCode'] : null;
        
        if($this->email && $this->password && is_null($this->authCode)){
            $this->requestAuthCode();
        }
    }
    
    /**
     * JoinMe requires exchanging an email address and password for an authCode
     */
    public function requestAuthCode()
    {
        $url = 'https://secure.join.me/API/requestAuthCode'.
                '?email='.$this->email.
                '&password='.$this->password;
        
        $response = Utilities::callApi($url);
        $validate = preg_match('/^AUTHCODE: (.*)$/', $response, $matches);
        if($validate){
            $this->authCode = $matches[1];
        }
    }
    
    public function getAuthCode()
    {
        return $this->authCode;
    }
    
    public function isAuthenticated()
    {
        if(isset($this->authCode)){
            return true;
        } else {
            return false;
        }
    }
}