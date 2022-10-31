<?php

declare(strict_types=1);

namespace App\Model\Works\ReadModel;

class IndexWork
{
    public $id;
    public $directory;
    public $name;
    public $filename;
    public $cover = null;
    public $link;

    public function __construct()
    {
        if ($this->filename) {
            $this->cover = $this->directory . '/' . $this->filename;
        }
    }
}