<?php

declare(strict_types=1);

namespace App\Model\Buyer\Repository;

use App\Model\Buyer\Entity\Buyer;
use App\Model\Buyer\Entity\Network;
use App\Model\Cpanel\UseCase\Filter\Buyers\Filter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class BuyerRepository
{
    private $em;
    private $connection;
    private $repo;
    private $paginator;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Buyer::class);
        $this->paginator = $paginator;
    }

    public function add(Buyer $buyer): void
    {
        $this->em->persist($buyer);
    }

    public function remove(Buyer $buyer): void
    {
        $this->em->remove($buyer);
    }

    public function get(int $id): Buyer
    {
        /** @var Buyer $buyer */
        if (!$buyer = $this->repo->find($id)) {
            throw new \DomainException('buyer.not.found');
        }
        return $buyer;
    }

    public function getByEmail(string $email): Buyer
    {
        $email = mb_strtolower($email);
        /** @var Buyer $buyer */
        if (!$buyer = $this->repo->findOneBy(['information.email' => $email])) {
            throw new \DomainException('buyer.not.found');
        }
        return $buyer;
    }

    public function duplicateEmail(string $email, int $currentId = 0): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->andWhere('t.id != :id')
                ->setParameter(':email', $email)
                ->setParameter(':id', $currentId)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $token
     * @return Buyer|object|null
     */
    public function findByResetToken(string $token): ?Buyer
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    public function existsByResetToken(string $token): bool
    {
        return $this->repo->createQueryBuilder('tk')
                ->select('COUNT (tk.id)')
                ->where('tk.resetToken.token = :token')
                ->setParameter(':token', $token)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function getForAuthByNetwork(string $network, string $identity): Buyer
    {
        $buyer = $this->em->createQueryBuilder()
            ->select('buyer')
            ->from(Buyer::class, 'buyer')
            ->innerJoin(Network::class, 'network', Expr\Join::WITH, 'buyer.id = network.buyer')
            ->where('network.network = :network AND network.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$buyer) {
            throw new \DomainException('buyer.not.found');
        }

        return $buyer;
    }

    public function hasByEmail(string $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.information.email = :email')
                ->setParameter(':email', $email)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->em->createQueryBuilder()
                ->select('COUNT(network)')
                ->from(Network::class, 'network')
                ->where('network.network = :network AND network.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function search(Filter $filter, int $currentPage, int $countElement): PaginationInterface
    {
        $buyer = $this->em->createQueryBuilder()
            ->select('buyer')
            ->from(Buyer::class, 'buyer');

        if ($filter->query) {
            $buyer->andWhere('LOWER(buyer.information.email) LIKE :query OR LOWER(buyer.information.name) LIKE :query OR LOWER(buyer.information.phone) LIKE :query');
            $buyer->setParameter(':query', '%' . mb_strtolower($filter->query) . '%');
        }
        $buyer->orderBy('buyer.id', 'DESC');

        return $this->paginator->paginate($buyer, $currentPage, $countElement);
    }
}

