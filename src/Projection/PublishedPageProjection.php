<?php

namespace CMS\Core\Projection;

use Doctrine\DBAL\Connection;
use CMS\Core\Event\PagePublish;
use Patchlevel\EventSourcing\Attribute\Projector;
use Patchlevel\EventSourcing\Attribute\Setup;
use Patchlevel\EventSourcing\Attribute\Subscribe;
use Patchlevel\EventSourcing\Attribute\Teardown;
use Patchlevel\EventSourcing\Subscription\Subscriber\SubscriberUtil;
use Symfony\Component\Serializer\SerializerInterface;

#[Projector('published_page')]
final class PublishedPageProjection
{
    use SubscriberUtil;
    public function __construct(
        private readonly Connection $db,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @return null|array{
     *     page_id: string,
     *     title: string,
     *     path: string,
     *     content_class: string,
     *     content_json: string,
     *     published_at: string,
     * }
     */
    public function getPageByPath(string $path): ?array
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from($this->table())
            ->where('path = :path')
            ->setParameter('path', $path)
            ->setMaxResults(1)
            ->fetchAssociative();
    }

    #[Subscribe(PagePublish::class)]
    public function onPagePublish(PagePublish $event, \DateTimeImmutable $recordedOn): void
    {
        $this->db->insert(
            $this->table(),
            [
                'page_id' => $event->page->id()->toString(),
                'title' => $event->page->title(),
                'path' => $event->path,
                'content_class' => get_class($event->page),
                'content_json' => $this->serializer->serialize($event->page, 'json'),
                'published_at' => $recordedOn->format('Y-m-d H:i:s'),
            ],
        );
    }

    #[Setup]
    public function create(): void
    {
        $this->db->executeStatement(<<<SQL
CREATE TABLE IF NOT EXISTS {$this->table()} (
    page_id VARCHAR(36) NOT NULL,
    title VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    content_class VARCHAR(255) NOT NULL,
    content_json JSON NOT NULL,
    published_at TIMESTAMP NOT NULL,
    PRIMARY KEY (page_id, published_at)
)
SQL);
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