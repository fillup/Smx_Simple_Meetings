<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\JoinMe;
use Smx\SimpleMeetings\Shared\HttpRequest;

/**
 * Utilities class to perform common functions for Join.Me API activities.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Utilities
{
    public static function callApi($url)
    {
        $request = HttpRequest::request($url);
        $success = preg_match('/^OK\n(.*)/ms',$request,$success_matches);
        if($success){
            return $success_matches[1];
        } else {
            $error = preg_match('/^ERROR: (\d+); (.*)$/', $request, $matches);
            if($error){
                throw new \ErrorException($matches[2],$matches[1]);
            } else {
                throw new \ErrorException('An API Error Occured: '.$request,'300');
            }
        }
    }
}