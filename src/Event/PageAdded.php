<?php

namespace CMS\Core\Event;

use CMS\Core\Id\WebspaceId;
use CMS\Core\Normalizer\PageNormalizer;
use CMS\Core\Page\PageInterface;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('leaf.page.added')]
final class PageAdded
{
    public function __construct(
        public readonly WebspaceId $webspaceId,
        public readonly string $path,
        public readonly string $serialzedPage,
    )
    {
    }
}