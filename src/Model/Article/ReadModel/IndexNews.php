<?php

declare(strict_types=1);

namespace App\Model\Article\ReadModel;

class IndexNews
{
    public $id;
    public $name;
    public $date;
    public $datetime;
    public $directory;
    public $filename;
    public $cover = null;
    public $link = '';

    public function __construct()
    {
        if ($this->filename) {
            $this->cover = $this->directory . '/' . $this->filename;
        }
    }
}