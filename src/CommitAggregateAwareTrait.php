<?php

namespace Traya;

trait CommitAggregateAwareTrait
{
    public function commit(AggregateRoot $aggregateRoot)
    {
        $eventPublisher = $this->getEventPublisher();
        $events = $aggregateRoot->pop();
        foreach($events as $event)
        {
            $eventPublisher->publish($event);
        }
    }

    protected abstract function getEventPublisher(): EventPublisherInterface;
}