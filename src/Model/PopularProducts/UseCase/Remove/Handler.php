<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Remove;

use App\Model\Flusher;
use App\Model\PopularProducts\Repository\PopularRepository;

class Handler
{
    private $populars;
    private $flusher;

    public function __construct(PopularRepository $populars, Flusher $flusher)
    {
        $this->populars = $populars;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $product = $this->populars->get($command->id);
        $this->populars->remove($product);
        $this->flusher->flush();
    }
}
