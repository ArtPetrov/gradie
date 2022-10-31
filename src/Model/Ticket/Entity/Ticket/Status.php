<?php

declare(strict_types=1);

namespace App\Model\Ticket\Entity\Ticket;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const OPEN = 'open';
    public const CLOSED = 'closed';

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $status;

    public function __construct(string $status = 'open')
    {
        Assert::oneOf($status, [
            self::OPEN,
            self::CLOSED
        ]);

        $this->status = $status;
    }

    public function isOpen(): bool
    {
        return $this->status === self::OPEN;
    }

    public function isClosed(): bool
    {
        return $this->status === self::CLOSED;
    }

    public function closed(): self
    {
        $this->status = self::CLOSED;
        return $this;
    }

}
