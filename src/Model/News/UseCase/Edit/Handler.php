<?php

declare(strict_types=1);

namespace App\Model\News\UseCase\Edit;

use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $command->news
            ->setHeader($command->header)
            ->setPublishedAt($command->publishedAt)
            ->setContent($command->content);
        $this->em->persist($command->news);
        $this->em->flush();
    }
}
