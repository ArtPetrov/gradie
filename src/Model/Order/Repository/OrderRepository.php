<?php

declare(strict_types=1);

namespace App\Model\Order\Repository;

use App\Model\Order\Entity\Order;
use App\Model\Order\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository
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
            throw new \DomainException('order.not.found');
        }
        return $order;
    }

    public function getByUuid(?string $uuid): Order
    {
        /** @var Order $order */
        if (!$order = $this->repo->findOneBy(['uuid' => $uuid])) {
            throw new \DomainException('order.not.found');
        }
        return $order;
    }

    public function findByUuid(?string $uuid): ?Order
    {
        if ($uuid) {
            return $this->repo->findOneBy(['uuid' => $uuid]);
        }
        return null;
    }

    public function getAll()
    {
        return $this->repo->findBy([], ['createdAt' => 'DESC']);
    }

    public function remove(Order $order): void
    {
        $this->em->remove($order);
    }

    public function countNew(): int
    {
        return (int)$this->repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->andWhere('w.status.status IN (:status)')
            ->setParameter(':status', [
                Status::PENDING_PAYMENT,
                Status::CLIENT_CHOSE_HELP_MANAGER,
                Status::IN_PROCESSING,
                Status::CLIENT_REFUSED_HELP
                ])
            ->getQuery()->getSingleScalarResult();
    }
}
