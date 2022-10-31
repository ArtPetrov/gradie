<?php

declare(strict_types=1);

namespace App\Model\Promocode\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="promocode")
 */
class Promocode implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    public const NAME_SESSION = 'promocode';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $enable = true;

    /**
     * @var float
     * @ORM\Column(type="float", options={"unsigned"=true})
     */
    private $value;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":0, "unsigned"=true})
     */
    private $used = 0;

    /**
     * @var Type
     * @ORM\Embedded(class="Type")
     */
    private $type;

    /**
     * @var Information
     * @ORM\Embedded(class="Information", columnPrefix=false)
     */
    private $information;

    /**
     * @var Restrictions
     * @ORM\Embedded(class="Restrictions")
     */
    private $restrictions;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(Type $type, float $value, bool $enable, Information $information, Restrictions $restrictions): self
    {
        $promo = (new self())
            ->changeType($type)
            ->editValue($value)
            ->updateInformation($information)
            ->changeRestrictions($restrictions);
        if (!$enable) {
            $promo->disable();
        }
        return $promo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getUsed(): int
    {
        return $this->used;
    }

    public function incrementUsed(int $count = 1): self
    {
        $this->used += $count;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function editValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function changeType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function updateInformation(Information $info): self
    {
        $this->information = $info;
        return $this;
    }

    public function getInformation(): Information
    {
        return $this->information;
    }

    public function changeRestrictions(Restrictions $restrictions): self
    {
        $this->restrictions = $restrictions;
        return $this;
    }

    public function getRestrictions(): Restrictions
    {
        return $this->restrictions;
    }

    public function enable(): self
    {
        $this->enable = true;
        return $this;
    }

    public function disable(): self
    {
        $this->enable = false;
        return $this;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function checkConstraints(float $sumOrder = 0): void
    {
        if (!$this->isEnable()) {
            throw new \DomainException('promocode.disable');
        }
        $this->getRestrictions()->isActiveDate(new \DateTimeImmutable());
        $this->getRestrictions()->checkLimitUsed($this->getUsed());
        $this->getRestrictions()->checkLimitSumOrder($sumOrder);
    }

    public function getDiscount(float $sumOrder = 0): float
    {
        $this->checkConstraints($sumOrder);

        if ($this->getValue() == 0) {
            return 0.0;
        }

        if ($this->getType()->isMoney()) {
            return $this->getValue();
        }

        if ($this->getType()->isProcent()) {
            return $sumOrder / 100 * $this->getValue();
        }
        return 0.0;
    }
}
