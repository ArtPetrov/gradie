<?php

declare(strict_types=1);

namespace App\Model\News\UseCase\Create;

use App\Model\News\Entity\News;
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
        $news = new News();
        $news
            ->setHeader($command->header)
            ->setPublishedAt($command->publishedAt)
            ->setContent($command->content);
        $this->em->persist($news);
        $this->em->flush();
    }
}
