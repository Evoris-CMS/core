<?php

namespace Evoris\Core\Projection;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Evoris\Core\Event\PageAdded;
use Evoris\Core\Id\WebspaceId;
use Patchlevel\EventSourcing\Attribute\Projector;
use Patchlevel\EventSourcing\Attribute\Setup;
use Patchlevel\EventSourcing\Attribute\Subscribe;
use Patchlevel\EventSourcing\Attribute\Teardown;

/**
 * @psalm-type PageData = array{
 *     id: string,
 *     webspace_id: string,
 *     path: string,
 *     title: string,
 *     slug: string,
 *     type: string,
 *     content: string,
 *     created_at: string,
 *     updated_at: string,
 *     deployed_at: string|null,
 * }
 */
#[Projector('pages')]
final class PageProjection
{
    public function __construct(
        private readonly Connection $connection
    )
    {
    }

    /**
     * @return array<PageData>
     */
    public function getPagesByWebspaceId(WebspaceId $webspaceId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->table())
            ->where('webspace_id = :webspace_id')
            ->setParameter('webspace_id', $webspaceId->toString())
            ->fetchAllAssociative();
    }

    /**
     * @return PageData|false
     */
    public function getPageByPath(string $host, string $path): array|false
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->table())
            ->where('path = :path')
            ->andWhere('host = :host')
            ->setParameter('path', $path)
            ->setParameter('host', $host)
            ->setMaxResults(1)
            ->fetchAssociative();
    }

    #[Subscribe(PageAdded::class)]
    public function onPageAdded(
        PageAdded $event,
    )
    {
        $this->connection->insert(
            $this->table(),
            [
                'id' => $event->pageId->toString(),
                'webspace_id' => $event->webspaceId->toString(),
                'host' => $event->host,
                'path' => $event->path,
                'title' => $event->title,
                'slug' => $event->slug,
                'type' => $event->type,
                'content' => $event->serializedPage,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
                'deployed_at' => null,
            ]
        );
    }

    #[Setup]
    public function create(): void
    {
        $this->connection->executeStatement("CREATE TABLE IF NOT EXISTS {$this->table()} (
            id VARCHAR(36) NOT NULL,
            webspace_id VARCHAR(36) NOT NULL,
            host VARCHAR(255) NOT NULL,
            path VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL,
            content text NOT NULL,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            deployed_at TIMESTAMP NULL
            );
        ");
    }

    #[Teardown]
    public function drop(): void
    {
        $this->connection->executeStatement("DROP TABLE IF EXISTS {$this->table()};");
    }

    private function table(): string
    {
        return 'projection_pages';
    }
}