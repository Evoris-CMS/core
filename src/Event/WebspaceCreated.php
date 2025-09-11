<?php
namespace Evoris\Core\Event;

use Evoris\Core\Id\WebspaceId;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('leaf.webspace.created')]
final class WebspaceCreated
{
    public function __construct(
        public readonly WebspaceId $webspaceId,
        public readonly string $host
    ) {
    }
}
