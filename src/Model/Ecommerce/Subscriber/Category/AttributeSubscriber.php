<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Subscriber\Category;

use App\Model\Ecommerce\Event\Attribute;
use App\Model\Ecommerce\UseCase\Category\Filter;
use App\Model\Ecommerce\Repository\AttributeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttributeSubscriber implements EventSubscriberInterface
{
    private $attributes;
    private $removeHandler;
    private $renameHandler;

    public function __construct(
        AttributeRepository $attributes,
        Filter\Remove\Handler $removeHandler,
        Filter\Rename\Handler $renameHandler
    )
    {
        $this->attributes = $attributes;
        $this->removeHandler = $removeHandler;
        $this->renameHandler = $renameHandler;

    }

    public static function getSubscribedEvents()
    {
        return [
            Attribute\RenamedEvent::class => 'renamed',
            Attribute\RemovedEvent::class => 'removed',
        ];
    }

    public function renamed(Attribute\RenamedEvent $event): void
    {
        $newSlug = $this->attributes->get($event->getIdAttribute())->getSlug();
        $command = new Filter\Rename\Command($event->oldSlug(), $newSlug);
        $this->renameHandler->handle($command);
    }

    public function removed(Attribute\RemovedEvent $event): void
    {
        $command = new Filter\Remove\Command($event->getSlug());
        $this->removeHandler->handle($command);
    }

}