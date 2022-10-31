<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Cancel\Remove;

use App\Model\Flusher;
use App\Model\Salon\Repository\ModerationSalonRepository;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;
    private $moderations;

    public function __construct(SalonRepository $salons, Flusher $flusher, ModerationSalonRepository $moderations)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
        $this->moderations = $moderations;
    }

    public function handle(Command $command): void
    {
        $salon = $this->salons->get($command->salon);
        $this->moderations->cancelRequestOfSalon($salon);
        $this->flusher->flush();
    }
}
