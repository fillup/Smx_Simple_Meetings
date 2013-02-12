<?php

namespace Smx\SimpleMeetings\Base;
use Smx\SimpleMeetings\Base\Site as SiteBase;

class Site implements \Smx\SimpleMeetings\Site
{
    private $sitename;
    private $username;
    private $password;
    
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
