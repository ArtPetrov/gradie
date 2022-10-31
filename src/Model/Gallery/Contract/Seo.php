<?php

declare(strict_types=1);

namespace App\Model\Gallery\Contract;

interface Seo
{
    public function getTitle(): ?string;

    public function getKeywords(): ?string;

    public function getDescription(): ?string;
}