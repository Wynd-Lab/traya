<?php

use PHPUnit\Framework\TestCase;
use Traya\EventInterface;
use Traya\AggregateRoot;
use Traya\CommitAggregateAwareTrait;
use Traya\EventPublisherInterface;

/**
 * @internal
 */
final class CommitAggregateAwareTraitTest extends TestCase
{
    /**
     * @test
     */
    public function GivenAnAggregate_WhenItIsCommitted_ThenEventsArePublished()
    {
        // Arrange
        $events = array();
        
        for($i = 0; $i < rand(1, 100); $i++) {
            array_push($events, new class(uniqid(), uniqid(), [uniqid()], [uniqid()]) implements EventInterface
            {
                /**
                 * @var string
                 */
                private $streamId;
    
                /**
                 * @var string
                 */
                private $type;
    
                /**
                 * @var array
                 */
                private $metadata;
    
                /**
                 * @var array
                 */
                private $payload;
    
                public function __construct(string $streamId, string $type, array $metadata, array $payload)
                {
                    $this->streamId = $streamId;
                    $this->type = $type;
                    $this->metadata = $metadata;
                    $this->payload = $payload;
                }
    
                public function getStreamId(): string
                {
                    return $this->streamId;
                }
    
                public function getType(): string
                {
                    return $this->type;
                }
    
                public function getMetadata(): array
                {
                    return $this->metadata;
                }
    
                public function getPayload(): array
                {
                    return $this->payload;
                }
            });
        }

        $aggregate = $this->getMockForAbstractClass(AggregateRoot::class);

        foreach($events as $event) {
            $aggregate->record($event);
        }

        $eventsCount = sizeof($aggregate->getUncommitedEvents());

        $commitAggregateTrait = $this->getMockForTrait(CommitAggregateAwareTrait::class);

        $eventPublisher = $this
            ->getMockBuilder(EventPublisherInterface::class)
            ->setMethods(['publish'])
            ->getMock()
        ;

        $commitAggregateTrait->method('getEventPublisher')->willReturn($eventPublisher);

        // Assert
        $eventPublisher
            ->expects($this->exactly($eventsCount))
            ->method('publish');

        // Act
        $commitAggregateTrait->commit($aggregate);
    }
}