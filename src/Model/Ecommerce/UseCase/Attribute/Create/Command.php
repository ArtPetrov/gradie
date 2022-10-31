<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\Create;

use Doctrine\Common\Collections\ArrayCollection;
use App\Model\Ecommerce\UseCase\Attribute\FieldValue;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull()
     * @Assert\Length(min=2)
     */
    public $name;

    /**
     * @Assert\NotNull()
     */
    public $type;

    public $values = [];

    public function __construct()
    {
        $this->values = new ArrayCollection();
        $this->values->add(new FieldValue\Command());
        $this->values->add(new FieldValue\Command());
    }
}
