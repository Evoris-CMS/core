<?php

namespace Evoris\Core\Service;

use Evoris\Core\Atrribute\Page;
use Evoris\Core\Exception\PageException;
use Evoris\Core\Id\PageId;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PageService
{
    public function __construct(private readonly array $classesByTag)
    {
    }
    
    public static function getPageName(object $page): string
    {
        $refl = new \ReflectionClass($page);
        $attributes = $refl->getAttributes(Page::class);
        
        if(count($attributes) === 0) {
            throw PageException::objectNotTypeOfPage($page);
        }

        $attribute = $attributes[0];

        return $attribute->newInstance()->name;
    }

    public static function getSlug(object $page): string
    {
        // Prefer a public property named 'slug'
        if (property_exists($page, 'slug')) {
            $value = $page->slug;
            if (!is_string($value) || $value === '') {
                throw PageException::slugPropertyInvalid($page, 'slug');
            }
            return $value;
        }

        // Fallback to accessor method if provided
        if (method_exists($page, 'slug')) {
            $value = $page->slug();
            if (!is_string($value) || $value === '') {
                throw PageException::slugPropertyInvalid($page, 'slug');
            }
            return $value;
        }

        throw PageException::slugPropertyMissing($page);
    }

    public static function getId(object $page): PageId
    {
        if (property_exists($page, 'id')) {
            $id = $page->id;
            if ($id instanceof PageId) {
                return $id;
            }
            // string support
            if (is_string($id) && $id !== '') {
                return PageId::fromString($id);
            }
        }

        if (method_exists($page, 'id')) {
            $id = $page->id();
            if ($id instanceof PageId) {
                return $id;
            }
            if (is_string($id) && $id !== '') {
                return PageId::fromString($id);
            }
        }

        throw PageException::idPropertyMissing($page);
    }
    
    

}