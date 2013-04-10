<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Shared;

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
    public static function request($uri,$method='GET',$postfields=false,$headers=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        /**
         * Added for debugging with Charles proxy
         */
        //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1');
        //curl_setopt($ch, CURLOPT_PROXYPORT, '8888');
        //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $method = strtoupper($method);
        if($method == 'GET'){
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } elseif($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif($method == 'PUT'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        } elseif($method == 'DELETE'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        if($postfields && ($method == 'POST' || $method == 'PUT')){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        }
        if($headers && is_array($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        if($response && !($info['http_code'] >= 400)){
            return $response;
        } else {
            if($info['http_code'] == 204){
                $result = array(
                    'success' => true
                );
                return json_encode($result);
            } else {
                $curl_errno = curl_errno($ch);
                if($curl_errno == 0){
                    $code = $info['http_code'];
                } else {
                    $code = $curl_errno;
                }
                throw new \ErrorException('Http Request Failed (HTTP Status: '.$info['http_code'].') with error: '.  
                    curl_error($ch), $code);
            }
        }
    }
}
