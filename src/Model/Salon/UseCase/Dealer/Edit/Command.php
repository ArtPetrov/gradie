<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Edit;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Salon\Entity\ModerationSalon;
use App\Model\Salon\Entity\Salon;
use App\Model\Salon\UseCase\Information;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public $id;
    public $dealer;

    /**
     * @Assert\Valid()
     * @var Information\Command
     */
    public $info;

    /**
     * @var Information\Command
     */
    public $moderation;

    public $moderationComment;
    public $comment;

    public static function fromSalon(Salon $salon, ?ModerationSalon $moderation, Dealer $dealer): self
    {
        $command = new self();
        $command->id = $salon->getId();
        $command->dealer = $dealer->getId();
        $command->info = Information\Command::fromInformation($salon->getInfo());
        $command->comment = is_null($moderation) ?'':$moderation->getComment();
        $command->moderation = is_null($moderation) ? new Information\Command() : Information\Command::fromInformation($moderation->getInfo());
        return $command;
    }
}
