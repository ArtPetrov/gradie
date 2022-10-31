<?php

declare(strict_types=1);

namespace App\Model\Payment\Repository;

use App\Model\Payment\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

class PaymentRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Payment::class);
    }

    public function add(Payment $payment): void
    {
        $this->em->persist($payment);
    }

    public function getById(int $id): Payment
    {
        /** @var Payment $payment */
        if (!$payment = $this->repo->find($id)) {
            throw new \DomainException('payment.not.found');
        }
        return $payment;
    }

    public function get(string $uuid): Payment
    {
        /** @var Payment $payment */
        if (!$payment = $this->repo->findBy(['uuid' => $uuid])) {
            throw new \DomainException('payment.not.found');
        }
        return $payment;
    }

    public function remove(Payment $payment): void
    {
        $this->em->remove($payment);
    }
}
