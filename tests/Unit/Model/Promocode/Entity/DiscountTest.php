<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Promocode\Entity;

use App\Model\Promocode\Entity\Information;
use App\Model\Promocode\Entity\Promocode;
use App\Model\Promocode\Entity\Restrictions;
use App\Model\Promocode\Entity\Type;
use PHPUnit\Framework\TestCase;

class DiscountTest extends TestCase
{
    /** @var \DateTimeImmutable */
    protected $now;
    /** @var Promocode */
    protected $promo;

    protected function setUp(): void
    {
        $this->now = new \DateTimeImmutable();
        $this->promo = Promocode::create(
            new Type(Type::PROCENT),
            10,
            true,
            new Information('TEST', 'Test key', ''),
            new Restrictions(4)
        );
    }

    public function testProcent(): void
    {
        $this->promo->changeType(new Type(Type::PROCENT));

        $this->promo->editValue(10.0);
        self::assertEquals($this->promo->getDiscount(500), 50);
        self::assertNotEquals($this->promo->getDiscount(500), 45);

        $this->promo->editValue(1.25);
        self::assertEquals($this->promo->getDiscount(100), 1.25);
        self::assertNotEquals($this->promo->getDiscount(500), 1.2);

        $this->promo->editValue(0);
        self::assertEquals($this->promo->getDiscount(500), 0);
        self::assertNotEquals($this->promo->getDiscount(500), 5);
    }

    public function testMoney(): void
    {
        $this->promo->changeType(new Type(Type::MONEY));

        $this->promo->editValue(100.0);
        self::assertEquals($this->promo->getDiscount(500), 100);
        self::assertNotEquals($this->promo->getDiscount(500), 45);

        $this->promo->editValue(0);
        self::assertEquals($this->promo->getDiscount(500), 0);
        self::assertNotEquals($this->promo->getDiscount(500), 5);
    }
}
