<?php

declare(strict_types=1);

namespace App\Model\Works\Contract;

interface Seo
{
    public function getTitle(): ?string;

    public function getKeywords(): ?string;

    public function getDescription(): ?string;
}