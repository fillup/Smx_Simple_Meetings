<?php

namespace Smx\SimpleMeetings\Base;

class ItemList implements \Iterator
{
    private $position = 0;
    private $items = array();
    
    public function __construct() {
        $this->position = 0;
    }
    
    /*
     * Add a Meeting, Attende, or User object to list
     * @param Meeting|Attendee|User Object should extend 
     *  \Smx\SimpleMeetings\Base\Meeting|Attendee|User
     */
    public function addItem($item){
        $this->items[] = $item;
    }
    
    /*
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