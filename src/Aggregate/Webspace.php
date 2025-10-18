<?php

namespace Evoris\Core\Aggregate;

use Evoris\Core\Command\AddPage;
use Evoris\Core\Command\CreateWebspace;
use Evoris\Core\Command\DeployPage;
use Evoris\Core\Event\PageAdded;
use Evoris\Core\Event\PagePublish;
use Evoris\Core\Event\PageRemoved;
use Evoris\Core\Event\PageUpdated;
use Evoris\Core\Event\WebspaceCreated;
use Evoris\Core\Id\PageId;
use Evoris\Core\Id\WebspaceId;
use Evoris\Core\Page\PageInterface;
use Evoris\Core\Service\PageService;
use Evoris\Core\Service\PageUtillity;
use Patchlevel\EventSourcing\Aggregate\BasicAggregateRoot;
use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Aggregate;
use Patchlevel\EventSourcing\Attribute\Apply;
use Patchlevel\EventSourcing\Attribute\Handle;
use Patchlevel\EventSourcing\Attribute\Id;
use Symfony\Component\Serializer\SerializerInterface;

#[Aggregate(name: 'evorisWebspace')]
final class Webspace extends BasicAggregateRoot
{
    #[Id]
    private WebspaceId $id;

    private string $host;

    /** @var array<string,object> */
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

    private function extractPath(string $slug, ?string $parentId): string
    {
        $path = [$slug];

        while($parentId !== null) {
            $parent = $this->pages[$parentId];
            $path[] = $parent['slug'];
            $parentId = $parent['parent'];
        }

        return "/".implode('/', array_reverse($path));
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

        $pageName = PageService::getPageName($page);

        $serializedPage = $serializer->serialize($page, 'json');
        $deserializedPage = $serializer->deserialize($serializedPage, $page::class, 'json');

        if(serialize($page) !== serialize($deserializedPage)) {
            throw new \Exception('Page is not valid');
        }

        $this->recordThat(new PageAdded(
            $this->id,
            PageId::generate(),
            $command->parentId,
            $command->host,
            $this->extractPath($command->slug, $command->parentId?->toString()),
            $command->title,
            $command->slug,
            $pageName,
            $serializedPage,
        ));
    }

    #[Handle]
    public function deployPage(DeployPage $command, SerializerInterface $serializer): void
    {
        $this->recordThat(new PagePublish(
            $this->id,
            $command->path,
            $serializer->serialize($command->page, 'json')
        ));
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
        $id = $event->pageId->toString();
        if (!array_key_exists($id, $this->pages)) {
            $this->pages[$id] = [
                'slug' => $event->slug,
                'parent' => $event->parentId,
                'title' => $event->title,
                'type' => $event->type,
                'content' => $event->serializedPage
            ];
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
