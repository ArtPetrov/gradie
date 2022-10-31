<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\File\Rename;

use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $command->file->setOriginalFilename($command->name);
        $this->em->persist($command->file);
        $this->em->flush();
    }
}
