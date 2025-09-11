<?php

namespace Evoris\Core\Projection;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Evoris\Core\Event\PageAdded;
use Evoris\Core\Event\PageCreated;
use Evoris\Core\Event\PagePublish;
use Evoris\Core\Event\PageUpdated;
use Evoris\Core\Id\PageId;
use Patchlevel\EventSourcing\Attribute\Projector;
use Patchlevel\EventSourcing\Attribute\Setup;
use Patchlevel\EventSourcing\Attribute\Subscribe;
use Patchlevel\EventSourcing\Attribute\Teardown;
use Patchlevel\EventSourcing\Subscription\Subscriber\SubscriberUtil;
use Symfony\Component\Serializer\SerializerInterface;

#[Projector('draft_page')]
final class DraftPageProjection
{
    use SubscriberUtil;

    public function __construct(
        private readonly Connection $db,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @param PageId $pageId
     * @return array{
     *     page_id: string,
     *     title: string,
     *     slug: string,
     *     content_class: string,
     *     content_json: string,
     *     last_published_at: string|null,
     *     created_at: string,
     *     updated_at: string,
     * }
     * @throws \Doctrine\DBAL\Exception
     */
    public function findPageById(PageId $pageId): array
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from($this->table())
            ->where('page_id = :pageId')
            ->setParameter('pageId', $pageId)
            ->setMaxResults(1)
            ->fetchAssociative();
    }

    #[Subscribe(PageAdded::class)]
    public function onPageCreated(PageAdded $event, DateTimeImmutable $recordedOn): void
    {
        $this->db->insert(
            $this->table(),
            [
                'page_id' => $event->page->id()->toString(),
                'title' => $event->page->title(),
                'slug' => $event->page->slug(),
                'content_class' => get_class($event->page),
                'content_json' => $this->serializer->serialize($event->page, 'json'),
                'last_published_at' => null,
                'created_at' => $recordedOn->format('Y-m-d H:i:s'),
                'updated_at' => $recordedOn->format('Y-m-d H:i:s'),
            ],
        );
    }

    #[Subscribe(PageUpdated::class)]
    public function onPageUpdated(PageUpdated $event, DateTimeImmutable $recordedOn): void
    {
        $this->db->update(
            $this->table(),
            [
                'title' => $event->title,
                'slug' => $event->slug,
                'content_class' => get_class($event->content),
                'content_json' => $this->serializer->serialize($event->content, 'json'),
                'updated_at' => $recordedOn->format('Y-m-d H:i:s'),
            ],
            [
                'page_id' => $event->pageId->toString(),
            ],
        );
    }

    #[Subscribe(PagePublish::class)]
    public function onPagePublish(PagePublish $event, DateTimeImmutable $recordedOn): void
    {
        $this->db->update(
            $this->table(),
            [
                'last_published_at' => $recordedOn->format('Y-m-d H:i:s'),
                'updated_at' => $recordedOn->format('Y-m-d H:i:s'),
            ],
            [
                'page_id' => $event->pageId->toString(),
            ],
        );
    }

    #[Setup]
    public function create(): void
    {
        $this->db->executeStatement(<<<SQL
CREATE TABLE IF NOT EXISTS {$this->table()} (
    page_id VARCHAR(36) NOT NULL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content_class VARCHAR(255) NOT NULL,
    content_json JSON NOT NULL,
    last_published_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);
SQL);
        // optional index on slug
        $this->db->executeStatement(
            "CREATE INDEX IF NOT EXISTS {$this->table()}_page_id_idx ON {$this->table()} (page_id);",
        );
    }

    #[Teardown]
    public function drop(): void
    {
        $this->db->executeStatement("DROP TABLE IF EXISTS {$this->table()};");
    }

    private function table(): string
    {
        return 'leafcms_' . $this->subscriberId();
    }
}
