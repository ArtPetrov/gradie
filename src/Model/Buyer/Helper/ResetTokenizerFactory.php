<?php

declare(strict_types=1);

namespace App\Model\Buyer\Helper;

use App\Model\Buyer\Service\ResetTokenizer;

class ResetTokenizerFactory
{
    public function create(string $interval): ResetTokenizer
    {
        return new ResetTokenizer(new \DateInterval($interval));
    }
}