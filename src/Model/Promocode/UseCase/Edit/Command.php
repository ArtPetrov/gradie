<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Edit;

use App\Model\Promocode\Entity\Promocode;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /** @var int */
    public $id;

    /** @var float */
    public $value = 0;

    public $type;

    /** @var bool */
    public $enable = true;

    /**
     * @var Information
     * @Assert\Valid()
     */
    public $information;

    /**
     * @var Restrictions
     * @Assert\Valid()
     */
    public $restrictions;

    public static function fromPromocode(Promocode $promocode): self
    {
        $command = new self();
        $command->id = $promocode->getId();
        $command->value = $promocode->getValue();
        $command->type = $promocode->getType()->getValue();
        $command->enable = $promocode->isEnable();
        $command->information = Information::fromPromocode($promocode->getInformation());
        $command->restrictions = Restrictions::fromPromocode($promocode->getRestrictions());
        return $command;
    }
}
