<?php

namespace Smx\SimpleMeetings\Shared;

class Time
{
    /**
     * Convert given local timestamp to a UTC timestamp.
     * 
     * @param int $timestamp
     * @return int
     */
    public static function getUtcTimestamp($timestamp=false)
    {
        if(!$timestamp){
            $timestamp = time();
        }
        
        // Get inverse
        $offset = -1 * date('Z');
        
        $utcTimestamp = $timestamp + $offset;
        
        return $utcTimestamp;
    }
}
