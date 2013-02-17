<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Base;

/**
 * HttpRequest class provides a very simple wrapper for using cURL functions.
 * 
 * Provides a single static function to make an HTTP request and get the results.
 * If there is an error returned from the server it will throw an \ErrorException
 * with the error message and error number returned from curl_exec.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 * 
 */
class HttpRequest
{   
    /**
     * Make an HTTP Request
     * 
     * Simplified interface to cURL methods.
     * 
     * @link http://php.net/curl
     * @param string $uri The URI to make the request to. For GET requests it
     *   should include all parameters. This is passed as the CURLOPT_URL
     * @param string $method Set to either GET (default) or POST
     * @param string|array $postfields If using POST, this parameter will be 
     *   set as CURLOPT_POSTFIELDS
     * @return string On successful HTTP request, it will return the body of the
     *   response as a string.
     * @throws \ErrorException On error making the HTTP request.
     */
    public static function request($uri,$method='GET',$postfields=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $uri);
        
        $method = strtoupper($method);
        if($method == 'GET'){
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        
        if($postfields && $method == 'POST'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        }
        
        $response = curl_exec($ch);
        if($response){
            return $response;
        } else {
            throw new \ErrorException('Http Request Failed with error: '.  
                    curl_error($ch),  curl_errno($ch));
        }
    }
}
