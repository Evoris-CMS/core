<?php

namespace Evoris\Core\Page;

use Evoris\Core\Atrribute as Core;
use Evoris\Core\Id\PageId;

#[Core\Page(name: 'page', template: '@evoris/page.html.twig')]
class Page
{
    public string $content;

    public function content(): string
    {
        return $this->content;
    }
}