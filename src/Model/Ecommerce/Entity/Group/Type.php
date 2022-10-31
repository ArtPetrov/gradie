<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Group;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const SELECT = 'SELECT';
    public const RADIO = 'RADIO';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $value;

    public function __construct(string $status = 'SELECT')
    {
        Assert::oneOf($status, [
            self::SELECT,
            self::RADIO
        ]);

        $this->value = $status;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
