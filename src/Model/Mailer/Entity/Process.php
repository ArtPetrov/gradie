<?php

declare(strict_types=1);

namespace App\Model\Mailer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Process
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_STOP = 'stop';
    public const STATUS_WORK = 'work';

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $status;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::STATUS_COMPLETED,
            self::STATUS_STOP,
            self::STATUS_WORK
        ]);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isStop():bool
    {
        return $this->status===self::STATUS_STOP;
    }

    public function isWork():bool
    {
        return $this->status===self::STATUS_WORK;
    }

    public function stop(): self
    {
        $this->status = self::STATUS_STOP;
        return $this;
    }

    public function run(): self
    {
        $this->status = self::STATUS_WORK;
        return $this;
    }

    public function completed(): self
    {
        $this->status = self::STATUS_COMPLETED;
        return $this;
    }
}
