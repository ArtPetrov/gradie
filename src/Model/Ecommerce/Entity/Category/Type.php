<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const BUY = 'BUY';
    public const VIEW = 'VIEW';

    /**
     * @ORM\Column(type="string", length=32, nullable=false, options={"default":"BUY"})
     */
    private $type;

    public function __construct(string $type='BUY')
    {
        Assert::oneOf($type, [
            self::BUY,
            self::VIEW
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
