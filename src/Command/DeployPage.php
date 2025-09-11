<?php

namespace CMS\Core\Command;

use CMS\Core\Id\WebspaceId;
use Patchlevel\EventSourcing\Attribute\Id;

final class DeployPage
{
    public function __construct(
        #[Id]
        public readonly WebspaceId $webspaceId,
        public readonly object $page
    )
    {
    }
}