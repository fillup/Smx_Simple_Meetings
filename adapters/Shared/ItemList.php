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
 * Generic Iteratable Object for storing lists of meetings, attendees, or users.
 * 
 * Class extends \Iterator and adds an addItem($item) method for adding objects
 * to the list.
 * 
 * @author Phillip Shipley <phillip@phillipshipley.com>
 */
class ItemList implements \Iterator
{
    private $position = 0;
    private $items = array();
    
    public function __construct() {
        $this->position = 0;
    }
    
    /**
     * Add a Meeting, Attende, or User object to list
     * 
     * @param Meeting|Attendee|User Object should extend 
     *  \Smx\SimpleMeetings\Base\Meeting|Attendee|User
     */
    public function addItem($item){
        $this->items[] = $item;
    }
    
    /**
     * Return size of list
     */
    public function size()
    {
        return count($this->items);
    }
    
    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->items[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->items[$this->position]);
    }
}