<?php

declare(strict_types=1);

namespace App\Model\Works\ReadModel;

use App\Model\Works\Entity\Work;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class WorkFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Work::class);
        $this->paginator = $paginator;
    }

    public function getLast(int $count = 5, int $offset = 0): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'work.id',
                'work.name as name',
                'image.directory as directory',
                'image.filename as filename'
            )
            ->from('content_works', 'work')
            ->leftJoin('work', 'content_works_images', 'images_link', 'images_link.work_id = work.id AND images_link.cover=true')
            ->leftJoin('images_link', 'file', 'image', 'images_link.file_id = image.id')
            ->orderBy("work.position", 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, IndexWork::class);

        return $stmt->fetchAll();
    }
}
