<?php

namespace Traya;

interface EventInterface
{
    function getStreamId();
    function getType();
    function getMetadata();
    function getPayload();
}