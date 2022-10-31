<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Report;

class Command
{
    public $dateStart;
    public $dateEnd;

    public function __construct()
    {
        $this->dateStart = new \DateTimeImmutable("-1 month");
        $this->dateEnd = new \DateTimeImmutable();
    }
}
