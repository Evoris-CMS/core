<?php

namespace Evoris\Core\Exception;

class PageException extends \Exception implements NovaExceptionInterface
{
    public static function objectNotTypeOfPage(object $object): self
    {
        return new self(sprintf('Object of type %s is not of type Page', $object::class));
    }
}