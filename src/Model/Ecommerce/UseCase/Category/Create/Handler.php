<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Create;

use App\Model\Ecommerce\Entity\Category\Category;
use App\Model\Ecommerce\Entity\Category\Seo;
use App\Model\Ecommerce\Entity\Category\Type;
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
        $category = new Category(
            $command->name,
            new Seo($command->seo->title, $command->seo->keywords, $command->seo->description, $command->seo->content),
            new Type($command->type),
            $command->filters
        );

        if ($command->parent) {
            $category->setParent($this->categories->get($command->parent));
        }

        $this->categories->add($category);

        $this->flusher->flush();
    }

}
