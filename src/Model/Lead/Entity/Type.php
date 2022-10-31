<?php

declare(strict_types=1);

namespace App\Model\Lead\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const CLIENT = 'Розница';
    public const COMPANY = 'Опт';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, [
            self::CLIENT,
            self::COMPANY
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
