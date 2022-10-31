<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Search;

use App\Model\Ecommerce\Helper\SortableParser;
use App\Model\Ecommerce\Helper\FiltersParser;
use Symfony\Component\HttpFoundation\Request;

class Command
{
    public $category;
    public $sort;
    public $limit = 24;
    public $filters = [];
    public $offset = 0;

    public static function fromFrontendRequest(Request $request): self
    {
        $command = new self();
        $command->limit += 1;
        $command->category = $request->request->get('category', '/');
        $command->offset = (int)$request->request->getInt('offset', 0);
        $command->sort = $request->request->get('sort', ['slug' => 'popular', 'order' => 'ASC']);
        $command->filters = $request->request->get('filters', []);
        return $command;
    }

    public static function fromQueryRequest(Request $request, string $slugCategory = '/'): self
    {
        $command = new self();
        $command->category = $slugCategory;
        $command->sort = SortableParser::getSort($request->query->get('sorting', ''));
        $command->filters = FiltersParser::getFilters($request->query->get('filters', ''));
        return $command;
    }
}
