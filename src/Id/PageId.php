<?php

namespace CMS\Core\Id;

use Patchlevel\EventSourcing\Aggregate\AggregateRootId;
use Patchlevel\EventSourcing\Aggregate\RamseyUuidV7Behaviour;

class PageId implements AggregateRootId
{
    use RamseyUuidV7Behaviour;
}