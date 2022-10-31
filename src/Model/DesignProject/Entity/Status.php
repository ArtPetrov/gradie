<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const NEW = 'NEW';
    public const INWORK = 'INWORK';
    public const CLOSED = 'CLOSED';
    public const COMPLETED = 'COMPLETED';
    public const CANCELED = 'CANCELED';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $status;

    public function __construct(string $status = 'NEW')
    {
        Assert::oneOf($status, [
            self::NEW,
            self::INWORK,
            self::CLOSED,
            self::COMPLETED,
            self::CANCELED,
        ]);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLabel(): string
    {
        return array_flip(self::list())[$this->getStatus()];
    }

    public static function list(): array
    {
        return [
            'Новая' => self::NEW,
            'В работе' => self::INWORK,
            'Закрыта' => self::CLOSED,
            'Выполнена' => self::COMPLETED,
            'Отменена' => self::CANCELED
        ];
    }
}
