<?php

namespace CMS\Core\Command;

use CMS\Core\Id\WebspaceId;
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