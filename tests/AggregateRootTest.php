<?php

use PHPUnit\Framework\TestCase;
use Traya\AggregateRoot;

final class AggregateRootTest extends TestCase
{
    /**
     * @test
     */
    public function WhenCreateAnInstance_ThenAnInstanceIsCreated()
    {
        $actual = new AggregateRoot();
        $this->assertTrue(true);
    }
}