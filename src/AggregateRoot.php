<?php

declare(strict_types=1);

namespace Traya;

class AggregateRoot 
{
    /**
     * @var EventInterface[]
     */
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    /**
     * @return EventInterface[]
     */
    public function getUncommitedEvents(): array
    {
        return $this->events;
    }

    public function record(EventInterface $event): void
    {
        array_push($this->events, $event);
    }
}