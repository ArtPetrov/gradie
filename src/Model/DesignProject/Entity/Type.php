<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const TYPE_1 = 'C-образная';
    public const TYPE_2 = 'П-образная';
    public const TYPE_3 = 'Г-образная';
    public const TYPE_4 = 'Другая форма';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, [
            self::TYPE_1,
            self::TYPE_2,
            self::TYPE_3,
            self::TYPE_4
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
