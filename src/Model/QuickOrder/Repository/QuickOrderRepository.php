<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\Repository;

use App\Model\QuickOrder\Entity\Order;
use App\Model\QuickOrder\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;

class QuickOrderRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Order::class);
    }

    public function add(Order $order): void
    {
        $this->em->persist($order);
    }

    public function get(int $id): Order
    {
        /** @var Order $order */
        if (!$order = $this->repo->find($id)) {
            throw new \DomainException('quick.order.not.found');
        }
        return $order;
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['id' => 'DESC']);
    }

    public function remove(Order $order): void
    {
        $this->em->remove($order);
    }

    public function countNew(): int
    {
        return (int)$this->repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->andWhere('w.status.status = :status')
            ->setParameter(':status', Status::NEW)
            ->getQuery()->getSingleScalarResult();
    }
}
