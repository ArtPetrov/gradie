<?php

declare(strict_types=1);

namespace App\Model\Order\Entity\Invoice;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const MAIN = 'MAIN';
    public const SHIPPING = 'SHIPPING';
    public const ADDITIONAL = 'ADDITIONAL';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $value;

    public function __construct(string $status = 'MAIN')
    {
        Assert::oneOf($status, [
            self::MAIN,
            self::SHIPPING,
            self::ADDITIONAL
        ]);

        $this->value = $status;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
