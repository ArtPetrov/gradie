<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Subscriber\Product;

use App\Model\Ecommerce\Event\Attribute;
use App\Model\Ecommerce\UseCase\Product;
use App\Model\Ecommerce\Repository\AttributeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttributeSubscriber implements EventSubscriberInterface
{

    private $attributes;
    private $removeHandler;
    private $renameHandler;
    private $rewriteHandler;

    public function __construct(
        AttributeRepository $attributes,
        Product\Attribute\Remove\Handler $removeHandler,
        Product\Attribute\Rename\Handler $renameHandler,
        Product\Attribute\Rewrite\Handler $rewriteHandler
    )
    {
        $this->attributes = $attributes;
        $this->removeHandler = $removeHandler;
        $this->renameHandler = $renameHandler;
        $this->rewriteHandler = $rewriteHandler;
    }

    public static function getSubscribedEvents()
    {
        return [
            Attribute\RenamedEvent::class => 'renamed',
            Attribute\RemovedEvent::class => 'removed',
            Attribute\RewriteValuesEvent::class => 'rewriteValues',
        ];
    }

    public function renamed(Attribute\RenamedEvent $event): void
    {
        $newSlug = $this->attributes->get($event->getIdAttribute())->getSlug();
        $command = new Product\Attribute\Rename\Command($event->oldSlug(), $newSlug);
        $this->renameHandler->handle($command);
    }

    public function removed(Attribute\RemovedEvent $event): void
    {
        $command = new Product\Attribute\Remove\Command($event->getSlug());
        $this->removeHandler->handle($command);
    }

    public function rewriteValues(Attribute\RewriteValuesEvent $event): void
    {
        $slug = $this->attributes->get($event->getId())->getSlug();
        $command = new Product\Attribute\Rewrite\Command($slug, $event->oldValues(), $event->newValues());
        $this->rewriteHandler->handle($command);
    }

}