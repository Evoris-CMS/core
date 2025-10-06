<?php

namespace Evoris\Core\Service;

use Evoris\Core\Atrribute\Id;
use Evoris\Core\Atrribute\Page;
use Evoris\Core\Atrribute\Slug;
use Evoris\Core\Atrribute\Title;
use Evoris\Core\Exception\PageException;
use Evoris\Core\Id\PageId;

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

        $attribute = $attributes[0];

        return $attribute->newInstance()->name;
    }

    public static function getSlug(object $page): string
    {
        $refl = new \ReflectionClass($page);

        foreach ($refl->getProperties() as $property) {
            $attrs = $property->getAttributes(Slug::class);
            if ($attrs === []) {
                continue;
            }

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            if (!$property->isInitialized($page)) {
                throw PageException::slugPropertyUninitialized($page, $property->getName());
            }

            $value = $property->getValue($page);

            if (!is_string($value)) {
                throw PageException::slugPropertyInvalid($page, $property->getName());
            }

            return $value;
        }

        throw PageException::slugPropertyMissing($page);
    }

    public static function getId(object $page): PageId
    {
        $refl = new \ReflectionClass($page);
        foreach ($refl->getProperties() as $property) {
            $attrs = $property->getAttributes(Id::class);
            if ($attrs === []) {
                continue;
            }

            if(!$property->isPublic()) {
                $property->setAccessible(true);
            }

            $id = (string) $property->getValue($page);

            return PageId::fromString($id);
        }

        throw PageException::idPropertyMissing($page);
    }

    public static function getTitle(object $page): string
    {
        $refl = new \ReflectionClass($page);

        foreach ($refl->getProperties() as $property) {
            $attrs = $property->getAttributes(Title::class);
            if ($attrs === []) {
                continue;
            }

            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            if (!$property->isInitialized($page)) {
                throw PageException::titlePropertyUninitialized($page, $property->getName());
            }

            $value = $property->getValue($page);

            if (!is_string($value)) {
                throw PageException::titlePropertyInvalid($page, $property->getName());
            }

            return $value;
        }

        throw PageException::titlePropertyMissing($page);
    }

}