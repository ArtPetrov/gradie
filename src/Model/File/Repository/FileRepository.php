<?php

declare(strict_types=1);

namespace App\Model\File\Repository;

use App\Model\File\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function get(int $id): File
    {
        return $this->find($id);
    }

    public function findByFilename(string $filename, ?string $directory = null): ?File
    {
        if ($directory) {
            return $this->findOneBy(['filename' => $filename, 'directory' => $directory]);
        }
        return $this->findOneBy(['filename' => $filename]);
    }

    public function getFilesById(array $ids)
    {
        $ids = array_filter($ids, static function ($id) {
            return strlen(trim($id)) > 0 && is_numeric($id);
        });

        if (count($ids) == 0) {
            return null;
        }
        return $this->findBy(['id' => $ids]);
    }
}
