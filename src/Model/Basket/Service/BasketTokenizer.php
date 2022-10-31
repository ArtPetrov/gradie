<?php

declare(strict_types=1);

namespace App\Model\Basket\Service;

use App\Model\Basket\Entity\BasketToken;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class BasketTokenizer
{
    public function generate(): BasketToken
    {
        return new BasketToken(
            Uuid::uuid4()->toString()
        );
    }

    public function isValid(?string $uuid): bool
    {
        try {
            Assert::notEmpty($uuid);
            Assert::uuid($uuid);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
}
