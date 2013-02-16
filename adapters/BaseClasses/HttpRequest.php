<?php

namespace Smx\SimpleMeetings\Base;

class HttpRequest
{   
    public static function request($uri,$method='GET',$fields=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $uri);
        
        $method = strtoupper($method);
        if($method == 'GET'){
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        
        if($fields && $method == 'POST'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
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
