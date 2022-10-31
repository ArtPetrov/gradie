<?php

declare(strict_types=1);

namespace App\Model\Article\Contract;

interface Seo
{
    public function getTitle(): ?string;

    public function getKeywords(): ?string;

    public function getDescription(): ?string;
}