<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Flusher
{
    private $em;
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function flush(EventBus ...$roots): void
    {
        $this->em->flush();

        foreach ($roots as $root) {
            foreach ($root->releaseEvents() as $event) {
                $this->dispatcher->dispatch($event);
            }
        }
    }
}
