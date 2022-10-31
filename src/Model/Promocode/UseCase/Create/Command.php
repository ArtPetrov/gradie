<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
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

    public function __construct()
    {
        $this->information = new Information();
        $this->restrictions = new Restrictions();
    }
}
