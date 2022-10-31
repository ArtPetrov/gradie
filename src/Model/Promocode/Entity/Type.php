<?php

declare(strict_types=1);

namespace App\Model\Promocode\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const PROCENT = 'PROCENT';
    public const MONEY = 'MONEY';

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::PROCENT,
            self::MONEY,
        ]);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isProcent(): bool
    {
        return $this->getValue() == self::PROCENT;
    }

    public function isMoney(): bool
    {
        return $this->getValue() == self::MONEY;
    }

    public static function list(): array
    {
        return [
            'Проценты' => self::PROCENT,
            'Сумма' => self::MONEY,
        ];
    }
}
