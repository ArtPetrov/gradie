<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Client
{
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $contact;

    public function __construct(string $name, string $contact)
    {
        $this->name = $name;
        $this->contact = $contact;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContact(): string
    {
        return $this->contact;
    }
}
