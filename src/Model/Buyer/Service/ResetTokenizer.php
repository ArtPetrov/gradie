<?php

declare(strict_types=1);

namespace App\Model\Buyer\Service;

use App\Model\Buyer\Entity\ResetToken;
use Ramsey\Uuid\Uuid;

class ResetTokenizer
{
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): ResetToken
    {
        return new ResetToken(
            Uuid::uuid4()->toString(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }
}