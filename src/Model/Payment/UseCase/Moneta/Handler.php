<?php

declare(strict_types=1);

namespace App\Model\Payment\UseCase\Moneta;

use App\Model\Flusher;
use App\Model\Order\Entity\Status;
use App\Model\Payment\Entity\Payment;
use App\Model\Payment\Repository\PaymentRepository;
use Psr\Container\ContainerInterface;

class Handler
{
    private $flusher;
    private $container;
    private $payments;

    public function __construct(Flusher $flusher, ContainerInterface $container, PaymentRepository $payments)
    {
        $this->flusher = $flusher;
        $this->container = $container;
        $this->payments = $payments;
    }

    public function handle(Command $command): void
    {
        $payment = $this->payments->getById((int)$command->MNT_TRANSACTION_ID);
        $this->checkSignature($command, $payment);
        if (!(bool)$command->MNT_TEST_MODE && $payment->getStatus()->isWait()) {
            $payment->paid();
            $order = $payment->getOrder();
            if (count($order->getInvoicesForPay()) === 0) {
                $order->changeStatus(new Status(Status::IN_PROCESSING));
            }
            $this->flusher->flush();
        }
    }

    private function checkSignature(Command $command, Payment $payment): void
    {
        if ($command->MNT_SIGNATURE != $this->makeSignature($command, $payment)) {
            throw new \DomainException('Payment signature error');
        }
    }

    private function makeSignature(Command $command, Payment $payment): string
    {
        return md5(
            $this->container->getParameter("moneta_id") .
            $payment->getId() .
            $command->MNT_OPERATION_ID .
            number_format($payment->getSum(), 2, '.', '') .
            $command->MNT_CURRENCY_CODE .
            ($command->MNT_SUBSCRIBER_ID ?? '') .
            $command->MNT_TEST_MODE .
            $this->container->getParameter("moneta_secret")
        );
    }
}
