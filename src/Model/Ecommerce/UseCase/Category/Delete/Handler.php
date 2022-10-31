<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Delete;

use App\Model\Ecommerce\Event\Category\UpdatedFileEvent;
use App\Model\Ecommerce\Repository\CategoryRepository;
use App\Model\Flusher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Handler
{
    private $categories;
    private $flusher;
    private $dispatcher;

    public function __construct(CategoryRepository $categories, Flusher $flusher, EventDispatcherInterface $dispatcher)
    {
        $this->categories = $categories;
        $this->flusher = $flusher;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Command $command): void
    {
        $category = $this->categories->get($command->id);
        $this->categories->remove($category);
        $this->flusher->flush();
        $this->dispatcher->dispatch(new UpdatedFileEvent($command->id));
    }
}
