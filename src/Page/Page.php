<?php

namespace CMS\Core\Page;

use CMS\Core\Atrribute as Nova;
use CMS\Core\Id\PageId;

#[Nova\Page(name: 'page')]
class Page implements PageInterface
{
    public function __construct(
        #[Nova\Id]
        private PageId $id,
        #[Nova\Slug]
        private string $slug,
        #[Nova\Title]
        private string $title,
        private string $content,
        private ?PageId $parent = null,
    )
    {
    }


    public function id(): PageId
    {
        return $this->id;
    }

    public function parent(): ?PageId
    {
        return $this->parent;
    }

    public function content(): string
    {
        return $this->content;
    }

}