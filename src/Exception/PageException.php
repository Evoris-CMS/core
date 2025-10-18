<?php

namespace Evoris\Core\Exception;

class PageException extends \Exception implements NovaExceptionInterface
{
    public static function objectNotTypeOfPage(object $object): self
    {
        return new self(sprintf('Object of type %s is not of type Page', $object::class));
    }

    public static function pageNotFoundByName(string $name): self
    {
        return new self(sprintf('Page with name %s not found', $name));
    }

    public static function slugPropertyMissing(object $object): self
    {
        return new self(sprintf('Object of type %s is missing the Slug property', $object::class));
    }

    public static function slugPropertyUninitialized(object $object): self
    {
        return new self(sprintf('Object of type %s has an uninitialized Slug property', $object::class));;
    }

    public static function slugPropertyInvalid(object $object): self
    {
        return new self(sprintf('Object of type %s has an invalid Slug property', $object::class));;
    }

    public static function titlePropertyMissing(object $object): self
    {
        return new self(sprintf('Object of type %s is missing the Title property', $object::class));
    }

    public static function titlePropertyUninitialized(object $object): self
    {
        return new self(sprintf('Object of type %s has an uninitialized Title property', $object::class));;
    }

    public static function titlePropertyInvalid(object $object): self
    {
        return new self(sprintf('Object of type %s has an invalid Title property', $object::class));;
    }

    public static function idPropertyMissing(object $object): self
    {
        return new self(sprintf('Object of type %s is missing the Id property', $object::class));
    }
}