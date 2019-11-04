<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Traya\AggregateRoot;
use Traya\EventInterface;

final class AggregateRootTest extends TestCase
{
    /**
     * @test
     */
    public function WhenCreateAnInstance_ThenAnEmptyCollectionOfEventsIsAvailable()
    {
        // Arrange
        $expected = [];

        // Act
        $aggregate = new AggregateRoot();
        $actual = $aggregate->getUncommitedEvents();

        // Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function GivenAnEvent_WhenRecordByAnAggregate_ThenTheEventIsAvailableAsAnUncommitedEvents()
    {
        // Arrange
        $expected = new class(uniqid(), uniqid(), [uniqid()], [uniqid()]) implements EventInterface
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
        };

        $aggregate = new AggregateRoot();
        $aggregate->record($expected);
        $events = $aggregate->getUncommitedEvents();
        $actual = $events[0];

        $this->assertEquals($expected, $actual);
    }
}