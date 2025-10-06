<?php
namespace Evoris\Core\Event;


use Evoris\Core\Id\WebspaceId;
use Evoris\Core\Normalizer\PageNormalizer;
use Evoris\Core\Page\PageInterface;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('evoris.page.publish')]
final class PagePublish
{
    public function __construct(
        public readonly WebspaceId $webspaceId,
        public readonly string $path,
        public readonly string $pageSerialized,
    ) {}
}