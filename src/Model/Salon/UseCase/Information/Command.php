<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Information;

use App\Model\Salon\Entity\Information;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull(message="not.null")
     */
    public $address;

    /**
     * @Assert\NotNull(message="not.null")
     */
    public $name;

    public $timetable;
    public $phone;
    public $email;
    public $site;
    public $comment;

    public static function fromInformation(Information $info): self
    {
        $command = new self();
        $command->address = $info->getAddress();
        $command->name = $info->getName();
        $command->timetable = $info->getTimetable();
        $command->phone = $info->getPhone();
        $command->email = $info->getEmail();
        $command->site = $info->getSite();
        $command->comment = $info->getComment();
        return $command;
    }

    public static function fromInformationWithModeration(Information $info, ?Information $moderation): self
    {
        $command = new self();
        $command->address = $info->getAddress() !== $moderation->getAddress() ? $moderation->getAddress() : $info->getAddress();
        $command->name = $info->getName() !== $moderation->getName() ? $moderation->getName() : $info->getName();
        $command->timetable = $info->getTimetable() !== $moderation->getTimetable() ? $moderation->getTimetable() : $info->getTimetable();
        $command->phone = $info->getPhone() !== $moderation->getPhone() ? $moderation->getPhone() : $info->getPhone();
        $command->email = $info->getEmail() !== $moderation->getEmail() ? $moderation->getEmail() : $info->getEmail();
        $command->site = $info->getSite() !== $moderation->getSite() ? $moderation->getSite() : $info->getSite();
        $command->comment = $info->getComment() !== $moderation->getComment() ? $moderation->getComment() : $info->getComment();
        return $command;
    }
}
