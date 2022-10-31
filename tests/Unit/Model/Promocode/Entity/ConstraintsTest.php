<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Promocode\Entity;

use App\Model\Promocode\Entity\Information;
use App\Model\Promocode\Entity\Promocode;
use App\Model\Promocode\Entity\Restrictions;
use App\Model\Promocode\Entity\Type;
use PHPUnit\Framework\TestCase;

class ConstraintsTest extends TestCase
{
    /** @var \DateTimeImmutable  */
    protected $now;
    /** @var Promocode  */
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

    public function testEnable(): void
    {
        self::assertTrue($this->promo->isEnable());
        $this->promo->disable();
        self::assertFalse($this->promo->isEnable());
    }

    public function testCountUsedLimit(): void
    {
        $this->promo->incrementUsed(3);
        $this->promo->checkConstraints(100);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('promocode.count.ended');
        $this->promo->incrementUsed(4);
        $this->promo->checkConstraints(120);
    }

    public function testDateLimitStart(): void
    {
        $this->promo->changeRestrictions(new Restrictions(1, 0.0, 0.0, $this->now->modify('+1 day'), $this->now->modify('next week')));
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('promocode.not.active.start');
        $this->promo->checkConstraints(0);
    }

    public function testDateLimitStop(): void
    {
        $this->promo->changeRestrictions(new Restrictions(1, 0.0, 0.0, $this->now->modify('-3 days'), $this->now->modify('-1 day')));
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('promocode.not.active.end');
        $this->promo->checkConstraints(0);
    }

    public function testMinOrderSum():void
    {
        $this->promo->changeRestrictions(new Restrictions(1, 500.00));
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('promocode.limit.sum.min');
        $this->promo->checkConstraints(100);
    }

    public function testMaxOrderSum():void
    {
        $this->promo->changeRestrictions(new Restrictions(1, 0.0, 50.00));
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('promocode.limit.sum.max');
        $this->promo->checkConstraints(100);
    }
}
