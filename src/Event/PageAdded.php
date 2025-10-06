<?php

namespace Evoris\Core\Event;

use Evoris\Core\Id\PageId;
use Evoris\Core\Id\WebspaceId;
use Evoris\Core\Normalizer\PageNormalizer;
use Evoris\Core\Page\PageInterface;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('evoris.page.added')]
final class PageAdded
{
    public function __construct(
        public readonly WebspaceId $webspaceId,
        public readonly PageId $pageId,
        public readonly ?PageId $parentId,
        public readonly string $path,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $type,
        public readonly string $serializedPage,
    )
    {
    }
}