<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Payment\Entity;

use App\Model\Order\Entity\BasketToken;
use App\Model\Order\Entity\Contact;
use App\Model\Order\Entity\Invoice\Invoice;
use App\Model\Order\Entity\Invoice\Type;
use App\Model\Order\Entity\Order;
use App\Model\Payment\Entity\Payment;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateTest extends TestCase
{
    /** @var Payment  */
    protected $payment;
    /** @var Order  */
    protected $order;

    protected function setUp(): void
    {
        $this->order = Order::create(new Contact(), new BasketToken(Uuid::uuid4()->toString()));
        $this->order->createInvoice(Invoice::create($this->order,new Type(),600.50));
        $this->order->createInvoice(Invoice::create($this->order,new Type(Type::SHIPPING),500.00));
        $this->payment = Payment::createForOrder($this->order);
    }

    public function testCreate(): void
    {
        self::assertEquals($this->payment->getSum(),$this->order->getTotalPrice());
    }

    public function testPaidRepeat(): void
    {
        $this->payment->paid();
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('payment.is.paid');
        $this->payment->paid();
    }

    public function testPaymentReturnOrder():void
    {
        $order = $this->payment->getOrder();
        self::assertTrue($order instanceof Order);
    }
}
