<?php

declare(strict_types=1);

namespace App\Model\Order\Service;

use App\Model\Order\Entity\Order;
use App\Model\Order\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CurrentOrder
{
    public const NAMING = 'order-uuid';
    /** @var Request|null */
    private $request;
    private $orders;

    public function __construct(RequestStack $request, OrderRepository $orders)
    {
        $this->request = $request->getCurrentRequest();
        $this->orders = $orders;
    }

    public function getUuid(): ?string
    {
        $uuid = $this->request->query->get(self::NAMING, null) ?? $this->request->getSession()->get(self::NAMING, null);
        if ($uuid) {
            $this->request->getSession()->set(self::NAMING, $uuid);
            return $uuid;
        }
        return null;
    }

    public function closedSession(): void
    {
        $this->request->getSession()->set(self::NAMING, null);
    }

    public function getOrder(): ?Order
    {
        if ($uuid = $this->getUuid()) {
            $order = $this->orders->findByUuid($uuid);
            if (!$order || !$order->getStatus()->inProcessCreating()) {
                $this->closedSession();
                return null;
            }
            return $order;
        }
        return null;
    }
}
