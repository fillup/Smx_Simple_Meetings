<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


namespace Smx\SimpleMeetings\Base;
use Smx\SimpleMeetings\Base\Site;

/**
 * User class provides methods for adding and listing users.
 * 
 * User class extends the Site class in order to inherit common fields
 * such as username, password, and sitename.  Base class primariy provides
 * a common user model and each adapter that extends User must implement
 * actual functionality for interacing with APIs.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class User extends Site
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
    
    public function __construct($username = false, $password = false, $sitename = false, $options = false) {
        parent::__construct($username, $password, $sitename);
        if(is_array($options)){
            foreach ($options as $option => $value){
                $this->$option = $value;
            }
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
                    122
                    );
        }
    }
}