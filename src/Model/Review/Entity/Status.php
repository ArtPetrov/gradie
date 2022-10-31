<?php

declare(strict_types=1);

namespace App\Model\Review\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::STATUS_WAIT,
            self::STATUS_ACTIVE,
            self::STATUS_BLOCKED
        ]);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }
}
