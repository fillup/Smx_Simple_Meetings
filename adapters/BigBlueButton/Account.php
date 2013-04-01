<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\BigBlueButton;
use Smx\SimpleMeetings\Base\Account as AccountBase;
use Smx\SimpleMeetings\BigBlueButton\Utilities;

/**
 * Account class to store baseUrl and security salt for API calls
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class Account extends AccountBase implements \Smx\SimpleMeetings\Account
{
    
    public $baseUrl = null;
    public $salt = null;
    
    public function __construct($authInfo){
        $authInfo['authType'] = Account::AUTH_SHAREDSECRET;
        parent::__construct($authInfo);
        $this->setAuthInfo($authInfo);
    }
    
    public function setAuthInfo($authInfo){
        parent::setAuthInfo($authInfo);
        $this->baseUrl = isset($authInfo['baseUrl']) ? 
                $this->checkAndFixBaseUrl($authInfo['baseUrl']) : null;
        $this->salt = isset($authInfo['salt']) ? 
                $authInfo['salt'] : null;
    }
    
    public function isAuthenticated(){
        if(!is_null($this->baseUrl) && !is_null($this->salt)){
            return true;
        } else {
            return false;
        }
    }
    
    public function checkAndFixBaseUrl($baseUrl){
        if(!preg_match('/^http[s]{0,1}\:\/\//',$baseUrl)){
            $baseUrl = 'http://'.$baseUrl;
        }
        if(substr($baseUrl,-1) != '/'){
            $baseUrl .= '/';
        }
        
        return $baseUrl;
    }
}
