<?php

namespace Evoris\Core\Id;

use Patchlevel\EventSourcing\Aggregate\AggregateRootId;
use Patchlevel\EventSourcing\Aggregate\RamseyUuidV7Behaviour;

class WebspaceId implements AggregateRootId
{
    use RamseyUuidV7Behaviour;
}