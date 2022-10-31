<?php

declare(strict_types=1);

namespace App\Model\Order\Subscriber;

use App\Controller\ErrorHandler;
use App\Model\Order\Event;
use App\Model\Order\Repository\OrderRepository;
use App\Model\Order\Service\OrderEmailNotify;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSubscriber implements EventSubscriberInterface
{
    private $orders;
    private $emailNotify;
    private $errors;

    public function __construct(
        OrderRepository $orders,
        OrderEmailNotify $emailNotify,
        ErrorHandler $errors
    )
    {
        $this->orders = $orders;
        $this->emailNotify = $emailNotify;
        $this->errors = $errors;
    }

    public static function getSubscribedEvents()
    {
        return [
            Event\CreateOrderEvent::class => 'notifyEmail',
        ];
    }

    public function notifyEmail(Event\CreateOrderEvent $event): void
    {
        try {
            $order = $this->orders->get($event->getIdOrder());
            $this->emailNotify->forBuyers($order);
        } catch (\DomainException $exception) {
            $this->errors->handle($exception);
        }
    }
}
