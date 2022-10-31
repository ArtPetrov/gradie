<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Edit;

use App\Model\Salon\Entity\ModerationSalon;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\UseCase\Dealer;
use App\Model\Salon\UseCase\Coordinates;
use App\Model\Salon\UseCase\Information;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;

    /**
     * @Assert\Valid()
     * @var Coordinates\Command
     */
    public $coords;

    /**
     * @Assert\Valid()
     * @var Information\Command
     */
    public $info;

    public $dealer;

    public $type;

    public static function fromSalon(Salon $salon, ?ModerationSalon $moderation): self
    {
        $command = new self();
        $command->id = $salon->getId();
        $command->coords = Coordinates\Command::fromCoords($salon->getCoords());

        $command->info = is_null($moderation) ?
            Information\Command::fromInformation($salon->getInfo()) :
            Information\Command::fromInformationWithModeration($salon->getInfo(), $moderation->getInfo());

        $command->type = $salon->getType()->getType();
        if ($owner = $salon->getOwner()) {
            $command->dealer = Dealer\Command::fromOwner($owner);
        }
        return $command;
    }
}
