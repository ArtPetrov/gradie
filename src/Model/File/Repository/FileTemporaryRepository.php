<?php

declare(strict_types=1);

namespace App\Model\File\Repository;

use App\Model\File\Entity\FileTemporary;
use App\Model\File\Entity\File;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class FileTemporaryRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(FileTemporary::class);
    }

    public function findAllDaysAgo(string $day = ''): array
    {
        $date = new \DateTimeImmutable($day);
        $stmt = $this->connection->createQueryBuilder()
            ->select('file_id')
            ->from('file_temporary')
            ->where('updated_at < :date')
            ->setParameter(':date',  $date->format(DATE_ATOM))
            ->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function add(FileTemporary $element): void
    {
        $this->em->persist($element);
    }

    public function findByIdFile(int $id): FileTemporary
    {
        if (!$tmp = $this->repo->findOneBy(['file_id' => $id])) {
            throw new \DomainException('images.temporary.not.found');
        }
        return $tmp;
    }

    public function removeByFile(File $file): void
    {
        if (!$tmp = $this->repo->findOneBy(['file' => $file])) {
            throw new \DomainException('images.temporary.not.found');
        }
        $this->em->remove($tmp);
    }

    public function remove(FileTemporary $element): void
    {
        $this->em->remove($element);
    }
}