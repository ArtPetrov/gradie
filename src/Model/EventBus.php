<?php

declare(strict_types=1);

namespace App\Model;

interface EventBus
{
    public function releaseEvents(): array;
}
