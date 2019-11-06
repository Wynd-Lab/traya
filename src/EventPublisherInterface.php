<?php

namespace Traya;

interface EventPublisherInterface
{
    function publish(EventInterface $event);
}