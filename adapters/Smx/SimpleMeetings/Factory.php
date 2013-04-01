<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings;

class Factory
{
    public static function SmxSimpleMeeting($ServiceProvider, $ObjectType, 
            $authInfo, $options=false)
    {
        $className = "\\Smx\\SimpleMeetings\\$ServiceProvider\\$ObjectType";
        if(class_exists($className)){
            return new $className($authInfo, $options);
        }
    }
}