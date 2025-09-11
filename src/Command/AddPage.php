<?php

namespace CMS\Core\Command;

use CMS\Core\Id\WebspaceId;
use CMS\Core\Page\Page;
use CMS\Core\Page\PageInterface;
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