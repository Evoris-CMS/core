<?php
namespace CMS\Core\Event;

use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('leaf.webspace.page_added')]
final class WebspacePageAdded
{
    public function __construct(
        public readonly Uuid $webspaceId,
        public readonly Uuid $pageId,
    ) {
    }
}
