<?php

declare(strict_types=1);

namespace Traya;

abstract class AggregateRoot 
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

    /**
     * @return EventInterface[]
     */
    public function pop(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * @param EventInterface[] events
     */
    public function load(array $events): void
    {
        foreach($events as $event)
        {
            $this->apply($event);
        }
    }

    protected abstract function apply(EventInterface $event): void;
}