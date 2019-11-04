<?php

namespace Traya;

class AggregateRoot 
{
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function getUncommitedEvents()
    {
        return $this->events;
    }

    public function record(EventInterface $event)
    {
        array_push($this->events, $event);
    }
}