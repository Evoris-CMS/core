<?php

namespace Evoris\Core\Atrribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Page
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $template = null,
    )
    {
    }
}