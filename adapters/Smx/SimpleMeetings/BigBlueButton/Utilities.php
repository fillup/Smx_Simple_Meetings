<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\BigBlueButton;
use Smx\SimpleMeetings\Shared\HttpRequest;

/**
 * Utilities class to perform common functions for BigBlueButton API activities.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class Utilities
{
    public static function callApi($baseUrl, $queryParams, $command, $salt, $method='GET', $data=null, $returnUrl=false)
    {
        $queryString = '';
        if(is_array($queryParams)){
            $first = true;
            foreach($queryParams as $name => $value){
                if($first){
                    $first = false;
                } else {
                    $queryString .= '&';
                }
                $queryString .= $name.'='.urlencode($value);
            }
        }
        
        $checksum = sha1($command.$queryString.$salt);
        $url = $baseUrl.$command.'/?'.$queryString.'&checksum='.$checksum;
        if($returnUrl){
            return $url;
        }
        $request = HttpRequest::request($url, $method);
        if($request){
            $xml = new \SimpleXMLElement($request);
            if($xml){
                if($xml->returncode == 'SUCCESS'){
                    return $xml;
                } else {
                    throw new \ErrorException('An API error occured ('.
                            $xml->messageKey.') '.$xml->message);
                }
            }
        } else {
            $error = "API call returned an HTTP status code of ".  curl_errno($ch);
            throw new \ErrorException($error,107);
        }
    }
}
