<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type1\Size;

use App\Model\DesignProject\UseCase\Client;
use App\Model\DesignProject\UseCase\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $a;

    /**
     * @Assert\NotBlank()
     */
    public $b;

    /**
     * @Assert\NotBlank()
     */
    public $c;

    /**
     * @Assert\NotBlank()
     */
    public $d;

    /**
     * @Assert\NotBlank()
     */
    public $e;

    /**
     * @Assert\NotBlank()
     */
    public $hp;

    /**
     * @Assert\NotBlank()
     */
    public $hd;

    /**
     * @Assert\NotBlank()
     */
    public $wd;

}
