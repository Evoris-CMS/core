<?php

namespace Evoris\Core\Service;

use Evoris\Core\Atrribute\Page;
use Evoris\Core\Exception\PageException;

class PageService
{
    public function __construct()
    {
    }
    
    public static function getPageName(object $page): string
    {
        $refl = new \ReflectionClass($page);
        $attributes = $refl->getAttributes(Page::class);
        
        if(count($attributes) === 0) {
            throw PageException::objectNotTypeOfPage($page);
        }

        return $attributes[0]->name;
    }
}