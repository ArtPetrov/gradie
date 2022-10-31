<?php

declare(strict_types=1);

namespace App\Model\Order\Entity\Invoice;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const CREATE = 'CREATE';
    public const PAID = 'PAID';
    public const CANCEL = 'CANCEL';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $value;

    public function __construct(string $status = 'CREATE')
    {
        Assert::oneOf($status, [
            self::CREATE,
            self::PAID,
            self::CANCEL
        ]);

        $this->value = $status;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function canBeCanceled(): bool
    {
        return $this->value !== self::PAID && $this->value !== self::CANCEL;
    }
}
