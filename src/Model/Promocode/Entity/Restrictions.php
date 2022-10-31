<?php

declare(strict_types=1);

namespace App\Model\Promocode\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Embeddable
 */
class Restrictions
{
    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $countLimit = 0;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    protected $dateStart;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    protected $dateEnd;

    /**
     * @var float
     * @ORM\Column(type="float", options={"unsigned"=true})
     */
    private $minSumOrder = 0;

    /**
     * @var float
     * @ORM\Column(type="float", options={"unsigned"=true})
     */
    private $maxSumOrder = 0;

    public function __construct(
        int $countLimit = 0,
        ?float $minSumOrder = 0,
        ?float $maxSumOrder = 0,
        ?\DateTimeImmutable $dateStart = null,
        ?\DateTimeImmutable $dateEnd = null
    )
    {
        $this->countLimit = $countLimit;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->minSumOrder = $minSumOrder;
        $this->maxSumOrder = $maxSumOrder;
    }

    public function getCountLimit(): int
    {
        return $this->countLimit;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    public function isDateStart(): bool
    {
        if (!$this->dateStart) {
            return false;
        }
        return $this->dateStart < (new \DateTimeImmutable());
    }

    public function isDateEnd(): bool
    {
        if (!$this->dateEnd) {
            return false;
        }
        return $this->dateEnd > (new \DateTimeImmutable());
    }

    public function isActiveDate(\DateTimeImmutable $now): void
    {
        if ($this->getDateStart() > $now && $this->getDateStart()) {
            throw new \DomainException('promocode.not.active.start');
        }
        if ($this->getDateEnd() < $now && $this->getDateEnd()) {
            throw new \DomainException('promocode.not.active.end');
        }
    }

    public function checkLimitUsed(int $used): void
    {
        if ($this->countLimit <= $used && $this->countLimit !== 0) {
            throw new \DomainException('promocode.count.ended');
        }
    }

    public function checkLimitSumOrder(float $sum = 0): void
    {
        if ($this->minSumOrder != 0) {
            if ($sum < $this->minSumOrder) {
                throw new \DomainException('promocode.limit.sum.min');
            }
        }
        if ($this->maxSumOrder != 0) {
            if ($sum > $this->maxSumOrder) {
                throw new \DomainException('promocode.limit.sum.max');
            }
        }
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    public function getMinSumOrder(): float
    {
        return $this->minSumOrder;
    }

    public function getMaxSumOrder(): float
    {
        return $this->maxSumOrder;
    }
}
