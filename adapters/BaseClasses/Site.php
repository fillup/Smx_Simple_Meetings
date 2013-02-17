<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Base;
use Smx\SimpleMeetings\Base\Site as SiteBase;

/**
 * Represents the most basic level of a web meetings site.
 * 
 * Class is used to store sitename/url, username, and password for use in each
 * child class to make api calls.
 */
class Site implements \Smx\SimpleMeetings\Site
{
    private $sitename;
    private $username;
    private $password;
    
    /**
     * 
     * @param String $username
     * @param String $password
     * @param String $sitename
     */
    public function __construct($username=false,$password=false,$sitename=false) {
        $this->sitename = $sitename ? $sitename : null;
        $this->username = $username ? $username : null;
        $this->password = $password ? $password : null;
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
