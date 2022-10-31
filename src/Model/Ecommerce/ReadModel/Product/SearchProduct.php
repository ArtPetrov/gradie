<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Product;

class SearchProduct
{
    public $id;
    public $article;
    public $name;
    public $cost;
    public $old_cost;
    public $link = '';
    public $filename;
    public $directory;
    public $cover = null;

    public function __construct()
    {
        if ($this->filename) {
            $this->cover = $this->directory . '/' . $this->filename;
        }
    }

}