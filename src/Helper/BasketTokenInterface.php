<?php

declare(strict_types=1);

namespace App\Helper;

interface BasketTokenInterface
{
    public function __construct(string $token);

    public function getToken(): ?string;

    public function isAvailable(): bool;
}
