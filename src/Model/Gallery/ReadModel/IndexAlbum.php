<?php

declare(strict_types=1);

namespace App\Model\Gallery\ReadModel;

class IndexAlbum
{
    public $id;
    public $directory;
    public $link;
    public $count;
    public $name;
    public $filename;
    public $cover = null;

    public function __construct()
    {
        if ($this->filename) {
            $this->cover = $this->directory . '/' . $this->filename;
        }
    }
}