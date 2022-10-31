<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Move;

use App\Model\Ecommerce\Entity\Category\Category;
use App\Model\Ecommerce\Repository\CategoryRepository;
use App\Model\Flusher;

class Handler
{
    private $flusher;
    private $categories;

    public function __construct(Flusher $flusher, CategoryRepository $categories)
    {
        $this->flusher = $flusher;
        $this->categories = $categories;
    }

    public function handle(Command $command): void
    {
        $category = $this->categories->get($command->id);

        if ($command->parent) {
            $command->parent = $this->categories->get($command->parent);
        }
        $categories = $this->categories->getRepository()->getChildren($command->parent, true, 'position', 'asc');

        $adjacent = null;
        $prevElement = null;
        $saveNextElement = false;

        /** @var Category $child */
        foreach ($categories as $child) {
            if ($saveNextElement) {
                $adjacent = $child;
            }
            if ($child->getId() === $category->getId()) {
                /** @var Category $adjacent */
                if ($command->direction === 'up') {
                    $adjacent = $prevElement;
                    break;
                }
                if ($command->direction === 'down') {
                    $saveNextElement = true;
                    continue;
                }
            }
            $saveNextElement = false;
            $prevElement = $child;
        }

        if ($adjacent) {
            $position = $adjacent->getPosition();
            $adjacent->setPosition($category->getPosition());
            $category->setPosition($position);
        }

        $this->flusher->flush();
    }

}
