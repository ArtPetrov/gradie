<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Promocode\Update;

use App\Model\Flusher;
use App\Model\Order\Entity\Promocode;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Promocode\Repository\PromocodeRepository;

class Handler
{
    private $flusher;
    private $orders;
    private $promocodes;

    public function __construct(Flusher $flusher, OrderRepository $orders, PromocodeRepository $promocodes)
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
        $this->promocodes = $promocodes;
    }

    public function handle(Command $command): void
    {
        if (!$this->promocodes->hasCode($command->promocode)) {
            return;
        }

        if (!$order = $this->orders->findByUuid($command->order)) {
            return;
        }

        $promocode = $this->promocodes->getByCode($command->promocode);
        $order->updatePromocode(Promocode::createFromPromocode($promocode));
        $this->flusher->flush($order);
    }
}
