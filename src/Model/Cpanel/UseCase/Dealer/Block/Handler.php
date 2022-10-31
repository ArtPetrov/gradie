<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Block;

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
        $command->dealer->moderation()->block();
        $this->em->persist($command->dealer);
        $this->em->flush();
    }
}
