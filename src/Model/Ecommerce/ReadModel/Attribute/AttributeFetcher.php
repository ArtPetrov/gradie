<?php
declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Attribute;

use App\Model\Ecommerce\Entity\Attribute\Attribute;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class AttributeFetcher
{
    private $connection;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Attribute::class);
    }

    public function getBySlug(string $slug, string $value)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'values'
            )
            ->from('ecommerce_attribute')
            ->where("slug =:slugs")
            ->setParameter('slugs', $slug, ParameterType::STRING)
            ->setMaxResults(1)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AttributeForProduct::class,[$value]);
        return $stmt->fetch();
    }

    public function getByList(array $attrs): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'slug',
                'name',
                'field_type',
                'values'
            )
            ->from('ecommerce_attribute')
            ->where("slug IN(:slugs)")
            ->setParameter('slugs', $attrs, Connection::PARAM_STR_ARRAY)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AttributeForFilter::class);
        return $stmt->fetchAll();
    }

    public function all()
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'slug',
                'name',
                'field_type',
                'values'
            )
            ->from('ecommerce_attribute')
            ->orderBy('name', 'asc')
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortAttribute::class, ['field_type']);
        return $stmt->fetchAll();
    }
}