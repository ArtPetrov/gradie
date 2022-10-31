<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Manager
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $comment;

    public function __construct(?string $comment = null)
    {
        $this->comment = $comment;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
