<?php

declare(strict_types=1);

namespace App\Model\File\UseCase\Category\Position;

class Command
{
    public $category;

    public $positions;

    public function __construct(int $category, string $positions)
    {
        $this->category = $category;
        $this->positions = explode(',',$positions);
    }
}
