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

    protected function apply(EventInterface $event): void
    {
        $eventName = get_class($event);
        $handlerMethodName = "on${eventName}";
        call_user_func_array(array($this, $handlerMethodName), array($event));
    }
}