<?php

namespace Evoris\Core\Tests\Service;

use Evoris\Core\Id\PageId;
use Evoris\Core\Page\Page;
use Evoris\Core\Service\PageService;
use PHPUnit\Framework\TestCase;

class PageServiceTest extends TestCase
{
    public function testPageName(): void
    {
        $id = PageId::generate();
        $page = new Page(
            $id,
            'test',
            'Test-Site',
            '<h1>Hello World</h1>',
            null
        );

        $pageName = PageService::getPageName($page);

        $this->assertEquals('page', $pageName);
    }

    public function testSlug(): void
    {
        $id = PageId::generate();
        $page = new Page(
            $id,
            'test',
            'Test-Site',
            '<h1>Hello World</h1>',
            null
        );

        $slug = PageService::getSlug($page);

        $this->assertEquals('test', $slug);
    }

    public function testId(): void
    {
        $id = PageId::generate();
        $page = new Page(
            $id,
            'test',
            'Test-Site',
            '<h1>Hello World</h1>',
            null
        );

        $pageId = PageService::getId($page);

        $this->assertInstanceOf(PageId::class, $pageId);
        $this->assertEquals($id->toString(), $pageId->toString());
    }
}