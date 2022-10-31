<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Edit;

use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Flusher;
use App\Model\Salon\UseCase\Information\Command as ModerationInfo;
use App\Model\Salon\Entity\Information;
use App\Model\Salon\Entity\ModerationSalon;
use App\Model\Salon\Repository\ModerationSalonRepository;
use App\Model\Salon\Repository\SalonRepository;

class Handler
{
    private $salons;
    private $flusher;
    private $dealers;
    private $moderations;

    public function __construct(Flusher $flusher, SalonRepository $salons, DealerRepository $dealers, ModerationSalonRepository $moderations)
    {
        $this->salons = $salons;
        $this->flusher = $flusher;
        $this->dealers = $dealers;
        $this->moderations = $moderations;
    }

    public function handle(Command $command): void
    {
        $salon = $this->salons->get((int)$command->id);
        $dealer = $this->dealers->get((int)$command->dealer);
        $info = new Information(
            $command->info->address,
            $command->info->name,
            $command->info->email,
            $command->info->phone,
            $command->info->site,
            $command->info->timetable,
            $command->info->comment
        );

        if (!$this->checkDiff($salon->getInfo(), $command->info)) {
            throw new \DomainException('salon.moderation.not.diff');
        }

        $moderation = ModerationSalon::create($salon, $dealer, $salon->getCoords(), $salon->getType(), $info, $command->comment);
        $this->moderations->cancelRequestOfSalon($salon);
        $this->moderations->add($moderation);
        $this->flusher->flush($moderation);
    }

    public function checkDiff(Information $salon, ModerationInfo $moderation): bool
    {
        if ($salon->getName() !== $moderation->name) {
            return true;
        }
        if ($salon->getAddress() !== $moderation->address) {
            return true;
        }
        if ($salon->getEmail() !== $moderation->email) {
            return true;
        }
        if ($salon->getPhone() !== $moderation->phone) {
            return true;
        }
        if ($salon->getSite() !== $moderation->site) {
            return true;
        }
        if ($salon->getTimetable() !== $moderation->timetable) {
            return true;
        }
        if ($salon->getComment() !== $moderation->comment) {
            return true;
        }
        return false;
    }
}
