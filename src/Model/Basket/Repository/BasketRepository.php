<?php

declare(strict_types=1);

namespace App\Model\Basket\Repository;

use App\Helper\BasketTokenInterface;
use App\Model\Basket\Entity\Item;
use App\Model\Ecommerce\Entity\Product\Product;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class BasketRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Item::class);
    }

    public function add(Item $item): void
    {
        $this->em->persist($item);
    }

    public function getItem(int $id): Item
    {
        /** @var Item $item */
        if (!$item = $this->repo->find($id)) {
            throw new \DomainException('item.not.found');
        }
        return $item;
    }

    public function getByTokenAndProduct(BasketTokenInterface $token, int $product): Item
    {
        /** @var Item $item */
        if (!$item = $this->repo->findOneBy(['token.token' => $token->getToken(), 'product' => $product])) {
            throw new \DomainException('item.not.found');
        }
        return $item;
    }

    public function findByTokenAndProduct(BasketTokenInterface $token, int $product): ?Item
    {
        /** @var $item Item */
        $item = $this->repo->findOneBy(['token.token' => $token->getToken(), 'product' => $product]);
        return $item;
    }

    public function findAllByToken(BasketTokenInterface $token): array
    {
        return $this->repo->findBy(['token.token' => $token->getToken()], ['id' => 'ASC']);
    }

    public function hasItemsForToken(BasketTokenInterface $token): bool
    {
        return $this->repo->createQueryBuilder('basket')
                ->select('COUNT(basket.id)')
                ->andWhere('basket.token.token = :token')
                ->setParameter(':token', $token->getToken())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function remove(Item $item): void
    {
        $this->em->remove($item);
    }

    public function removeByToken(BasketTokenInterface $token): void
    {
        $this->connection->createQueryBuilder()
            ->delete('basket_items', 'item')
            ->andWhere('item.token = :token')
            ->setParameter('token', $token->getToken())
            ->execute();
    }

    public function removeByTokenAndProduct(BasketTokenInterface $token, Product $product): void
    {
        $this->connection->createQueryBuilder()
            ->delete('basket_items', 'item')
            ->andWhere('item.token = :token')
            ->andWhere('item.product_id = :product')
            ->setParameter('token', $token->getToken())
            ->setParameter('product', $product->getId(), ParameterType::INTEGER)
            ->execute();
    }

    public function getTotalPriceForBasket(BasketTokenInterface $token): float
    {
        $products = $this->findAllByToken($token);
        $total = 0.00;
        foreach ($products as $product) {
            $total += $product->getCount() * $product->getProduct()->getFinishPrice();
        }
        return $total;
    }
}
