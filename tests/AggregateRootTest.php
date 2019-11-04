<?php

use PHPUnit\Framework\TestCase;
use Traya\AggregateRoot;

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
}