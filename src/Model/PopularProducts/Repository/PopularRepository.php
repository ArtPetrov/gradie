<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\Repository;

use App\Model\PopularProducts\Entity\PopularProducts;
use Doctrine\ORM\EntityManagerInterface;

class PopularRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(PopularProducts::class);
    }

    public function add(PopularProducts $popular): void
    {
        $this->em->persist($popular);
    }

    public function get(int $id): PopularProducts
    {
        /** @var PopularProducts $product */
        if (!$product = $this->repo->find($id)) {
            throw new \DomainException('slider.not.found');
        }
        return $product;
    }

    public function remove(PopularProducts $product): void
    {
        $this->em->remove($product);
    }
}
