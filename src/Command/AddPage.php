<?php

namespace Evoris\Core\Command;

use Evoris\Core\Id\WebspaceId;
use Evoris\Core\Page\Page;
use Evoris\Core\Page\PageInterface;
use Patchlevel\EventSourcing\Attribute\Id;

final readonly class AddPage
{
    public function __construct(
        #[Id]
        public WebspaceId $webspaceId,
        public object $page
    )
    {
    }
}