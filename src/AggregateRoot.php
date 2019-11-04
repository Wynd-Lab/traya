<?php

namespace Traya;

class AggregateRoot 
{
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    /**
     * Get the value of events
     */ 
    public function getUncommitedEvents()
    {
        return $this->events;
    }
}