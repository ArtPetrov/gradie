<?php

declare(strict_types=1);

namespace App\Model\Order\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Payment
{
    public const ONLINE = 'ONLINE';
    public const CASH = 'CASH';

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $type;

    public static function create(?string $type): self
    {
        Assert::oneOf($type, [
            self::ONLINE,
            self::CASH
        ]);
        $command = new self();
        $command->type = $type;
        return $command;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isCash(): bool
    {
        return $this->type === self::CASH;
    }

    public function isOnline(): bool
    {
        return $this->type === self::ONLINE;
    }
}
