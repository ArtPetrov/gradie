<?php

declare(strict_types=1);

namespace App\Model\Slider\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const INDEX = 'index';
    public const CONTEXT = 'context';

    /**
     * @ORM\Column(type="string", length=12)
     */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, [
            self::INDEX,
            self::CONTEXT
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isIndexSlider(): bool
    {
        return self::INDEX === $this->type;
    }

}
