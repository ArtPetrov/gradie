<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;

    public function __construct(SalonRepository $salons, Flusher $flusher)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $salon = $this->salons->get($command->id);
        $this->salons->remove($salon);
        $this->flusher->flush();
    }
}
