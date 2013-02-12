<?php
/*
 * 
 */
namespace Smx\SimpleMeetings;

if(!class_exists('\\Smx\\SimpleMeetings\\Base\\Site')){
    require_once __DIR__.'/../SmxSimpleMeetings.php';
}

class Factory
{
    public static function SmxSimpleMeeting($ServiceProvider, $ObjectType, 
            $username, $password, $sitename, $options=false)
    {
        $className = "\\Smx\\SimpleMeetings\\$ServiceProvider\\$ObjectType";
        if(class_exists($className)){
            return new $className($username, $password, $sitename, $options);
        }
    }
}