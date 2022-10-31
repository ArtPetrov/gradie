<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Create;

use App\Model\Flusher;
use App\Model\Salon\Entity\Coordinates;
use App\Model\Salon\Entity\Information;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\Entity\Type;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;

    public function __construct(Flusher $flusher, SalonRepository $salons)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $coords = new Coordinates($command->coords->lat, $command->coords->lon);

        $info = new Information(
            $command->info->address,
            $command->info->name,
            $command->info->email,
            $command->info->phone,
            $command->info->site,
            $command->info->timetable,
            $command->info->comment
        );

        $type = new Type($command->type);
        $salon = Salon::create($coords, $type, $info);

        if ($dealer = $command->dealer->dealer) {
            $salon->assignOwner($dealer);
        }

        $this->salons->add($salon);

        $this->flusher->flush($salon);
    }
}
