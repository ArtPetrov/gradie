<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

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
}
