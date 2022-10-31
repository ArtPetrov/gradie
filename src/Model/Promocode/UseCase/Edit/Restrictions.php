<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Edit;

use Symfony\Component\Validator\Constraints as Assert;
use App\Model\Promocode\Entity;

class Restrictions
{
    /**
     * @var \DateTimeImmutable|null
     */
    public $dateStart;
    /**
     * @var \DateTimeImmutable|null
     */
    public $dateEnd;

    /**
     * @var float
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public $minSumOrder = 0;

    /**
     * @var float
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public $maxSumOrder = 0;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public $countLimit = 0;

    public static function fromPromocode(Entity\Restrictions $restrictions): self
    {
        $command = new self();
        $command->dateStart = $restrictions->getDateStart();
        $command->dateEnd = $restrictions->getDateEnd();
        $command->minSumOrder = $restrictions->getMinSumOrder();
        $command->maxSumOrder = $restrictions->getMaxSumOrder();
        $command->countLimit = $restrictions->getCountLimit();
        return $command;
    }
}
