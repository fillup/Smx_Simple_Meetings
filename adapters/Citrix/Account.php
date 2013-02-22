<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\Citrix;
use Smx\SimpleMeetings\Base\Account as AccountBase;
use Smx\SimpleMeetings\Citrix\Utilities;

/**
 * 
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Account extends AccountBase implements \Smx\SimpleMeetings\Account
{
    private $apiKey = null;
    private $responseKey = null;
    private $accessToken = null;
    private $organizerKey = null;
    
    private $authUrlBase = 'https://api.citrixonline.com/oauth/authorize';
    private $accessRequestUrlBase = 'https://api.citrixonline.com/oauth/access_token?grant_type=authorization_code';
    
    public function __construct($authInfo) {
        $authInfo['authType'] = Account::AUTH_OAUTH;
        parent::__construct($authInfo);
        $this->setAuthInfo($authInfo);
    }
    
    public function setAuthInfo($authInfo) {
        parent::setAuthInfo($authInfo);
        $this->apiKey = isset($authInfo['apiKey']) ? 
                $authInfo['apiKey'] : null;
        $this->responseKey = isset($authInfo['responseKey']) ? 
                $authInfo['responseKey'] : null;
        $this->accessToken = isset($authInfo['accessToken']) ? 
                $authInfo['accessToken'] : null;
        
        if($this->apiKey && $this->responseKey && is_null($this->accessToken)){
            $this->authForAccessToken();
        }
    }
    
    public function authForAccessToken($responseKey=false){
        if($responseKey){
            $this->responseKey = $responseKey;
        }
        $url = $this->accessRequestUrlBase . '&code=' .$this->responseKey. 
                '&client_id=' .$this->apiKey;
        $auth = Utilities::callApi($url, false, 'GET');
        if($auth && is_object($auth)){
            if(isset($auth->access_token)){
                $this->accessToken = $auth->access_token;
            }
            if(isset($auth->organizer_key)){
                $this->organizerKey = $auth->organizer_key;
            }
        }
    }
    
    public function getAuthUrl()
    {
        return $this->authUrlBase . '?client_id=' . $this->apiKey;
    }
    
    public function getAccessToken()
    {
        return $this->accessToken;
    }
    
    public function getOrganizerKey()
    {
        return $this->organizerKey;
    }
    
    /**
     * Check if necessary credentials are in place for making API calls
     * 
     * @return boolean
     */
    public function isAuthenticated()
    {
        if(isset($this->accessToken)){
            return true;
        } else {
            return false;
        }
    }
}
