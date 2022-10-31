<?php

declare(strict_types=1);

namespace App\Model\Gallery\Repository;

use App\Model\Gallery\Entity\Album;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class AlbumRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Album::class);
    }

    public function add(Album $album): void
    {
        $this->em->persist($album);
    }

    public function get(int $id): Album
    {
        /** @var Album $page */
        if (!$album = $this->repo->find($id)) {
            throw new \DomainException('album.not.found');
        }
        return $album;
    }

    public function getAll()
    {
        return $this->repo->findBy([],['position'=>'DESC']);
    }

    public function remove(Album $album): void
    {
        $this->em->remove($album);
    }

    public function findByPosition(int $currentPosition, string $direction): ?int
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'position')
            ->from('content_gallery');

        if ($direction === 'up') {
            $stmt->andWhere('position > :position')->orderBy('position', 'ASC');
        } else {
            $stmt->andWhere('position < :position')->orderBy('position', 'DESC');
        }
        $stmt->setParameter('position', $currentPosition, ParameterType::INTEGER);
        $stmt->setMaxResults(1);
        return (int)$stmt->execute()->fetch()['id'];
    }

}
