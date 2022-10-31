<?php

declare(strict_types=1);

namespace App\Model\Payment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const WAIT = 'WAIT';
    public const PAID = 'PAID';
    public const ERROR = 'ERROR';
    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $value;

    public function __construct(string $status = 'WAIT')
    {
        Assert::oneOf($status, [
            self::WAIT,
            self::PAID,
            self::ERROR
        ]);

        $this->value = $status;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isPaid(): bool
    {
        return $this->getValue() === self::PAID;
    }

    public function isWait(): bool
    {
        return $this->getValue() === self::WAIT;
    }
}
