<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Assign;

use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Flusher;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;
    private $dealers;

    public function __construct(SalonRepository $salons, DealerRepository $dealers, Flusher $flusher)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
        $this->dealers = $dealers;
    }

    public function handle(Command $command): void
    {
        $salon = $this->salons->get($command->salon);
        $dealer = $this->dealers->get($command->dealer);

        if ($salon->existOwner($dealer)) {
            throw new \DomainException('dealer.not.assign.exist');
        }

        if ($salon->getOwner()) {
            throw new \DomainException('dealer.not.assign.busy');
        }

        $salon->assignOwner($dealer);

        $this->flusher->flush();
    }
}
