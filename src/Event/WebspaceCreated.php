<?php
namespace CMS\Core\Event;

use CMS\Core\Id\WebspaceId;
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
