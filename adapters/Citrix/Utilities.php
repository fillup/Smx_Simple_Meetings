<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Citrix;
use Smx\SimpleMeetings\Shared\HttpRequest;

/**
 * Utilities class to perform common functions for Citrix API activities.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Utilities
{
    public static function callApi($url, $auth_token, $method='POST', $data=null)
    {
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json"
        );
        
        if($auth_token){
            $headers[] = "Authorization: OAuth oauth_token=$auth_token";
        }
        
        $json_data = false;
        
        if(is_array($data)){
            $json_data = json_encode($data);
            $headers[] = "Content-Length: ".  strlen($json_data);
        }
        
        $request = HttpRequest::request($url, $method, $json_data, $headers);
        if($request){
            $results = json_decode($request);
            if(isset($results->int_err_code)){
                throw new \ErrorException('There was an error returned from the API: '.
                        $results->msg .' ('.$results->int_err_code.')',160);
            } elseif(isset($results->err)){
                throw new \ErrorException('There was an error returned from the API: '.
                        $results->message .' ('.$results->err.')',160);
            } else {
                return $results;
            }
        } else {
            $error = "API call returned an HTTP status code of ".  curl_errno($ch);
            throw new \ErrorException($error,107);
        }
    }
}
