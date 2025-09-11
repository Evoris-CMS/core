<?php

namespace Evoris\Core\Page;

use Evoris\Core\Id\PageId;

interface PageInterface
{
    public function id(): PageId;
    public function slug(): string;

    public function title(): string;
    public function parent(): ?PageId;

}