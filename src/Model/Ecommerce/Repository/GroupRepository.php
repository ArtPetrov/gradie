<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Repository;

use App\Model\Ecommerce\Entity\Group\Group;
use App\Model\Ecommerce\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;

class GroupRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Group::class);
    }

    public function add(Group $group): self
    {
        $this->em->persist($group);
        return $this;
    }

    public function get(int $id): Group
    {
        /** @var Group $group */
        if (!$group = $this->repo->find($id)) {
            throw new \DomainException('group.not.found');
        }
        return $group;
    }

    public function remove(Group $group): void
    {
        $this->em->remove($group);
    }

    public function findByProduct(Product $product): ?Group
    {
        $qb = $this->em->createQueryBuilder()
            ->select('g')
            ->from(Group::class, 'g');

        $qb->innerJoin(\App\Model\Ecommerce\Entity\Group\Product::class, 'p', Expr\Join::WITH,
            $qb->expr()->andX(
                $qb->expr()->eq('g', 'p.group'),
                $qb->expr()->eq('p.product', ':product')
            )
        );

        $qb->setParameter(':product', $product);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
