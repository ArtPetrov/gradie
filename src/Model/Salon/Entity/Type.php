<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const STORE = 'Магазин';
    public const BRAND_SALON = 'Фирменный салон';
    public const DEALER_SALON = 'Салон дилера';
    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, [
            self::STORE,
            self::BRAND_SALON,
            self::DEALER_SALON
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function getTypes(): array
    {
        return [
            self::STORE => self::STORE,
            self::BRAND_SALON => self::BRAND_SALON,
            self::DEALER_SALON => self::DEALER_SALON,
        ];
    }
}
