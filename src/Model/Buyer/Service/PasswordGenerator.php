<?php

declare(strict_types=1);

namespace App\Model\Buyer\Service;

class PasswordGenerator
{
    public static function generate(): string
    {
        return bin2hex(random_bytes(6));
    }
}