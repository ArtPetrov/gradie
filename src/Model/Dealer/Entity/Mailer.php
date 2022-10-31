<?php

declare(strict_types=1);

namespace App\Model\Dealer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Mailer
{
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $approve = true;

    public function unsubscribe(): self
    {
        $this->approve = false;
        return $this;
    }

    public function subscribe(): self
    {
        $this->approve = true;
        return $this;
    }

    public function approve(): bool
    {
        return $this->approve;
    }
}
