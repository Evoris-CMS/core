<?php

namespace Evoris\Core\Service;

use Evoris\Core\Exception\PageException;

final class PageRegistry
{
    /** @var array <string, array{class: string, template: string}> */
    private array $pages = [];

    public function __construct(
        iterable $pages,
    )
    {
        if($pages instanceof \Traversable) {
            $pages = iterator_to_array($pages);
        }

        $this->pages = $pages;
    }

    public function all(): array
    {
        return $this->pages;

    }

    /** @return array{class: string, template: string} */
    public function get(string $name): array
    {
        return $this->pages[$name] ?? throw PageException::pageNotFoundByName($name);
    }
}