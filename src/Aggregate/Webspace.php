<?php

namespace Evoris\Core\Aggregate;

use Evoris\Core\Command\AddPage;
use Evoris\Core\Command\CreateWebspace;
use Evoris\Core\Command\DeployPage;
use Evoris\Core\Event\PageAdded;
use Evoris\Core\Event\PageRemoved;
use Evoris\Core\Event\PageUpdated;
use Evoris\Core\Event\WebspaceCreated;
use Evoris\Core\Id\WebspaceId;
use Evoris\Core\Page\PageInterface;
use Evoris\Core\Service\PageUtillity;
use Patchlevel\EventSourcing\Aggregate\BasicAggregateRoot;
use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Aggregate;
use Patchlevel\EventSourcing\Attribute\Apply;
use Patchlevel\EventSourcing\Attribute\Handle;
use Patchlevel\EventSourcing\Attribute\Id;
use Symfony\Component\Serializer\SerializerInterface;

#[Aggregate(name: 'leafWebspace')]
final class Webspace extends BasicAggregateRoot
{
    #[Id]
    private WebspaceId $id;

    private string $host;

    /** @var array<string,PageInterface> */
    private array $pages = [];

    public function id(): WebspaceId
    {
        return $this->id;
    }

    public function host(): string
    {
        return $this->host;
    }

    /**
     * @return array<string> page ids as strings
     */
    public function pages(): array
    {
        return $this->pages;
    }

    /**
     * @param list<Uuid> $pages
     */
    public static function create(WebspaceId $id, string $host): self
    {
        $self = new self();
        $self->recordThat(new WebspaceCreated($id, $host));

        return $self;
    }

    private function extractPath(PageInterface $page): string
    {
        $parent = $page->parent();
        $path = [$page->slug()];

        while ($parent !== null) {
            $parent = $this->pages[$parent->toString()];
            $path[] = $parent->slug();
            $parent = $parent->parent();
        }

        $path = array_reverse($path);

        return implode('/', $path);
    }

    #[Handle]
    public function createWebspace(CreateWebspace $command): void
    {
        $this->recordThat(new WebspaceCreated(
            $command->id,
            $command->host
        ));
    }

    #[Handle]
    public function addPage(AddPage $command, SerializerInterface $serializer): void
    {
        $page = $command->page;

        // ignore if already present
        if (array_key_exists($page->id()->toString(), $this->pages)) {
            return;
        }

        $this->recordThat(new PageAdded(
            $this->id,
            $this->extractPath($page),
            $serializer->serialize([
                'name' => PageUtillity::getPageName($page),
                'object' => $page,
            ], )
        ));
    }
    
    public function deployPage(DeployPage $command): void
    {
        
    }

    public function updatePage(PageInterface $page): void
    {
        if(!array_key_exists($page->id()->toString(), $this->pages)) {
            return;
        }

        $this->recordThat(new PageUpdated($this->id, $this->extractPath($page), $page));
    }

    public function removePage(Uuid $pageId): void
    {
        if (!in_array($pageId->toString(), $this->pages, true)) {
            return; // nothing to remove
        }

        $this->recordThat(new PageRemoved($this->id, $pageId));
    }

    #[Apply]
    protected function applyWebspaceCreated(WebspaceCreated $event): void
    {
        $this->id = $event->webspaceId;
        $this->host = $event->host;
        $this->pages = [];
    }

    #[Apply]
    protected function applyPageAdded(PageAdded $event): void
    {
        $id = $event->page->id()->toString();
        if (!array_key_exists($id, $this->pages)) {
            $this->pages[$id] = $event->page;
        }
    }

    #[Apply]
    protected function applyPageUpdated(PageUpdated $event): void
    {
        $id = $event->page->id()->toString();
        if (array_key_exists($id, $this->pages)) {
            $this->pages[$id] = $event->page;
        }
    }

    #[Apply]
    protected function applyWebspacePageRemoved(PageRemoved $event): void
    {
        $id = $event->pageId->toString();
        $this->pages = array_values(array_filter(
            $this->pages,
            static fn (string $existing) => $existing !== $id
        ));
    }
}
