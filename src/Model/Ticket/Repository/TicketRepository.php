<?php

declare(strict_types=1);

namespace App\Model\Ticket\Repository;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Ticket\Entity\Ticket\State;
use App\Model\Ticket\Entity\Ticket\Status;
use App\Model\Ticket\Entity\Ticket\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findSortPublished()
    {
        return $this->findBy([], ['publishedAt' => 'DESC']);
    }

    public function findForAuthor(Dealer $dealer)
    {
        return $this->createQueryBuilder('ticket')
            ->andWhere('ticket.author = :author')
            ->setParameter('author', $dealer)
            ->orderBy('ticket.updatedAt', 'DESC')
            ->getQuery();
    }

    public function findbyStatus(string $status)
    {
        return $this->createQueryBuilder('ticket')
            ->andWhere('ticket.status.status = :status')
            ->setParameter('status', $status)
            ->orderBy('ticket.updatedAt', 'DESC')
            ->getQuery();
    }

    public function countNewForSupport():int
    {
        return (int)$this->createQueryBuilder('ticket')
            ->select('COUNT(ticket.id)')
            ->andWhere('ticket.status.status = :status')
            ->andWhere('ticket.state.state = :state')
            ->setParameter(':status', Status::OPEN)
            ->setParameter(':state', State::AUTHOR_ASKED)
            ->getQuery()->getSingleScalarResult();
    }


    public function countNewReplyForDealer(Dealer $dealer):int
    {
        return (int)$this->createQueryBuilder('ticket')
            ->select('COUNT(ticket.id)')
            ->andWhere('ticket.author = :dealer')
            ->setParameter(':dealer', $dealer)
            ->andWhere('ticket.status.status = :status')
            ->setParameter(':status', Status::OPEN)
            ->andWhere('ticket.state.state = :state')
            ->setParameter(':state', State::SUPPORT_REPLY)
            ->getQuery()->getSingleScalarResult();
    }

}
