<?php

declare(strict_types=1);

namespace App\Model\Gallery\ReadModel;

use App\Model\Gallery\Entity\Album;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class AlbumFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Album::class);
        $this->paginator = $paginator;
    }

    public function getLast(int $count = 5, int $offset = 0): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'album.id',
                'album.name_short as name',
                'image.directory as directory',
                'image.filename as filename',
                '(SELECT count(id) FROM content_gallery_images lk WHERE lk.album_id=album.id) as count'
            )
            ->from('content_gallery', 'album')
            ->leftJoin('album', 'content_gallery_images', 'images_link', 'images_link.album_id = album.id AND images_link.cover=true')
            ->leftJoin('images_link', 'file', 'image', 'images_link.file_id = image.id')
            ->orderBy("album.position", 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, IndexAlbum::class);

        return $stmt->fetchAll();
    }

}
