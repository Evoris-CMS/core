<?php

namespace Evoris\Core\Command;

use Evoris\Core\Id\WebspaceId;
use Patchlevel\EventSourcing\Attribute\Id;

final readonly class CreateWebspace
{
    public function __construct(
        #[Id]
        public WebspaceId $webspaceId,
        public string $name,
        public string $host,
    )
    {
    }
}