<?php

namespace Evoris\Core\Page;

use Evoris\Core\Atrribute as Core;
use Evoris\Core\Id\PageId;

#[Core\Page(name: 'page')]
class Page
{
    public function __construct(
        public string $content,
    )
    {
    }

    public function content(): string
    {
        return $this->content;
    }

}