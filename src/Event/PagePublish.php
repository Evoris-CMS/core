<?php
namespace CMS\Core\Event;


use CMS\Core\Id\WebspaceId;
use CMS\Core\Normalizer\PageNormalizer;
use CMS\Core\Page\PageInterface;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('leaf.page.publish')]
final class PagePublish
{
    public function __construct(
        public readonly WebspaceId $webspaceId,
        public readonly string $path,
        #[PageNormalizer()]
        public readonly PageInterface $page,
    ) {}
}