<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\ManagerSupport;

use App\Model\Flusher;
use App\Model\Order\Entity\Status;
use App\Model\Order\Repository\OrderRepository;

class Handler
{
    private $flusher;
    private $orders;

    public function __construct(Flusher $flusher, OrderRepository $orders)
    {
        $this->flusher = $flusher;
        $this->orders = $orders;
    }

    public function handle(Command $command): ?int
    {
        $order = $this->orders->findByUuid($command->order);
        if ($command->help === 'need') {
            $order->changeStatus(new Status(Status::CLIENT_CHOSE_HELP_MANAGER));
            $order->managerNeed();
        } else {
            $order->changeStatus(new Status(Status::CLIENT_REFUSED_HELP));
            $order->managerCancel();
        }
        $this->flusher->flush($order);

        return $order->isHelpNeed() ? $order->getId() : null;
    }
}