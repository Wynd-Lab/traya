<?php

declare(strict_types=1);

namespace Traya;

interface EventInterface
{
    function getType(): string;
    function getMetadata(): array;
    function getPayload(): array;
}