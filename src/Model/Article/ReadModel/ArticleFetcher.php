<?php

declare(strict_types=1);

namespace App\Model\Article\ReadModel;

use App\Model\Article\Entity\Article;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class ArticleFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Article::class);
        $this->paginator = $paginator;
    }

    public function getLast(int $count = 5,int $offset = 0): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'news.id',
                'news.name_short as name',
                'news.published_at as date',
                'image.directory as directory',
                'image.filename as filename'
                )
            ->from('content_news', 'news')
            ->leftJoin('news', 'content_news_images', 'images_link', 'images_link.news_id = news.id AND images_link.cover=true')
            ->leftJoin('images_link', 'file', 'image', 'images_link.file_id = image.id')
            ->orderBy("news.published_at", 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults($count)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, IndexNews::class);

        return $stmt->fetchAll();
    }

}
