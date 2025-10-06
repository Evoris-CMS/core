<?php

namespace Evoris\Core\Command;

use Evoris\Core\Id\WebspaceId;
use Patchlevel\EventSourcing\Attribute\Id;

final class DeployPage
{
    public function __construct(
        #[Id]
        public readonly WebspaceId $webspaceId,
        public readonly string $path,
        public readonly object $page
    )
    {
    }
}