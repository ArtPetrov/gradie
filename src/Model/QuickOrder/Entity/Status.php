<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const NEW = 'NEW';
    public const WORK = 'WORK';
    public const COMPLETE = 'COMPLETE';
    public const CANCEL = 'CANCEL';

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $status;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::NEW,
            self::WORK,
            self::COMPLETE,
            self::CANCEL,
        ]);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public static function list(): array
    {
        return [
            'Новый' => self::NEW,
            'В работе' => self::WORK,
            'Выполнен' => self::COMPLETE,
            'Отменен' => self::CANCEL,
        ];
    }
}
