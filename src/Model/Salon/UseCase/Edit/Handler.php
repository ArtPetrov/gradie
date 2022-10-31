<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Edit;

use App\Model\Flusher;
use App\Model\Salon\Entity\Coordinates;
use App\Model\Salon\Entity\Information;
use App\Model\Salon\Entity\Type;
use App\Model\Salon\Repository\ModerationSalonRepository;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;
    private $moderations;

    public function __construct(Flusher $flusher, SalonRepository $salons, ModerationSalonRepository $moderations)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
        $this->moderations = $moderations;
    }

    public function handle(Command $command): void
    {
        $salon = $this->salons->get((int)$command->id);

        $info = new Information(
            $command->info->address,
            $command->info->name,
            $command->info->email,
            $command->info->phone,
            $command->info->site,
            $command->info->timetable,
            $command->info->comment
        );
        $salon
            ->updateCoords(new Coordinates($command->coords->lat, $command->coords->lon))
            ->updateInfo($info)
            ->changeType(new Type($command->type));

        if(empty($command->dealer->id)&&($oldOwner = $salon->getOwner()))
        {
            $salon->removeOwner($oldOwner->getDealer());
        }

        if ($dealer = $command->dealer->dealer) {
            if (!$salon->existOwner($dealer)) {
                if ($oldOwner = $salon->getOwner()) {
                    $salon->removeOwner($oldOwner->getDealer());
                }
                $salon->assignOwner($dealer);
            }
        }

        $this->moderations->cancelRequestOfSalon($salon);

        $this->flusher->flush($salon);
    }
}
