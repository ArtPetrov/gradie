<?php

declare(strict_types=1);

namespace App\Model\Order\Repository;

use App\Model\Order\Entity\Invoice\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Invoice::class);
    }

    public function add(Invoice $invoice): void
    {
        $this->em->persist($invoice);
    }

    public function get(string $id): Invoice
    {
        /** @var Invoice $invoice */
        if (!$invoice = $this->repo->find($id)) {
            throw new \DomainException('invoice.not.found');
        }
        return $invoice;
    }

    public function remove(Invoice $invoice): void
    {
        $this->em->remove($invoice);
    }
}
