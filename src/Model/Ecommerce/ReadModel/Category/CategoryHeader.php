<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Category;

class CategoryHeader
{
    public $id;
    public $name;
    public $type;
    public $path;
    public $childs = [];
}