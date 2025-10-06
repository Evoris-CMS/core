<?php
namespace Evoris\Core\Event;

use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('evoris.webspace.page_added')]
final class WebspacePageAdded
{
    public function __construct(
        public readonly Uuid $webspaceId,
        public readonly Uuid $pageId,
    ) {
    }
}
