<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const PROCESS = 'PROCESS';
    public const PROCESS_DELETE = 'PROCESS_DELETE';
    public const ACCEPTED = 'ACCEPTED';
    public const REJECT = 'REJECT';
    public const CANCEL = 'CANCEL';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    public function __construct(string $status)
    {
        Assert::oneOf($status, [
            self::PROCESS,
            self::REJECT,
            self::ACCEPTED,
            self::PROCESS_DELETE,
            self::CANCEL,
            self::REJECT
        ]);

        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isProcess(): bool
    {
        return $this->status === self::PROCESS || $this->status === self::PROCESS_DELETE;
    }
}
