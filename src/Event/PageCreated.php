<?php
namespace Evoris\Core\Event;


use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('evoris.page.created')]
final class PageCreated
{
    public function __construct(
        public readonly Uuid $pageId,
        public readonly string $title,
        public readonly string $slug,
        public readonly object $content,
    ) {
    }
}