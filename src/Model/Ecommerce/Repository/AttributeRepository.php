<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Repository;

use App\Model\Ecommerce\Entity\Attribute\Attribute;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\FetchMode;

class AttributeRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Attribute::class);
    }

    public function sortByAlphabetically()
    {
        return $this->repo->findBy([], ['name' => 'ASC']);
    }

    public function findAll(): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'slug',
                'type'
            )
            ->from('ecommerce_attribute')
            ->orderBy('name', 'ASC')
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function add(Attribute $attribute): void
    {
        $this->em->persist($attribute);
    }

    public function get(int $id): Attribute
    {
        /** @var Attribute $attribute */
        if (!$attribute = $this->repo->find($id)) {
            throw new \DomainException('attribute.not.found');
        }
        return $attribute;
    }

    public function getBySlug(string $slug): Attribute
    {
        /** @var Attribute $attribute */
        if (!$attribute = $this->repo->findOneBy(['slug' => $slug])) {
            throw new \DomainException('attribute.not.found');
        }
        return $attribute;
    }

    public function remove(Attribute $attribute): void
    {
        $this->em->remove($attribute);
    }
}
