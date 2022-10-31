<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Remove;

use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Flusher;
use App\Model\Salon\Entity\ModerationSalon;
use App\Model\Salon\Repository\ModerationSalonRepository;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;
    private $dealers;
    private $moderations;

    public function __construct(SalonRepository $salons, DealerRepository $dealers, Flusher $flusher, ModerationSalonRepository $moderations)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
        $this->dealers = $dealers;
        $this->moderations = $moderations;
    }

    public function handle(Command $command): void
    {
        $salon = $this->salons->get($command->salon);
        $dealer = $this->dealers->get($command->dealer);

        $moderation = ModerationSalon::removeTicket($salon, $dealer, $salon->getCoords(), $salon->getType());
        $this->moderations->cancelRequestOfSalon($salon);
        $this->moderations->add($moderation);
        $this->flusher->flush($moderation);
    }
}
