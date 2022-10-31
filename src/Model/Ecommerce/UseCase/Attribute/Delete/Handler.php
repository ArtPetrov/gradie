<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\Delete;

use App\Model\Ecommerce\Event\Attribute\RemovedEvent as AttributeRemovedEvent;
use App\Model\Ecommerce\Repository\AttributeRepository;
use App\Model\Flusher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Handler
{
    private $dispatcher;
    private $attributes;
    private $flusher;

    public function __construct(AttributeRepository $attributes, Flusher $flusher, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->attributes = $attributes;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $attribute = $this->attributes->get($command->id);
        $this->attributes->remove($attribute);
        $this->flusher->flush();
        $this->dispatcher->dispatch(new AttributeRemovedEvent($command->id, $attribute->getSlug()));
    }
}
