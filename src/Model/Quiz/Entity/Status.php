<?php

declare(strict_types=1);

namespace App\Model\Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const NEW = 'NEW';
    public const VIEW = 'VIEW';

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $status;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::NEW,
            self::VIEW
        ]);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getName(): string
    {
        return $this->list()[$this->status];
    }

    public static function list(): array
    {
        return [
            self::NEW => 'Новый',
            self::VIEW => 'Просмотрено',
        ];
    }
}
