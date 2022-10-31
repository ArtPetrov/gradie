<?php

declare(strict_types=1);

namespace App\Model\News\Service;

use App\Model\File\Repository\FileRepository;

class SearchImages
{
    private $files;
    private $pathUploadPublic;

    public function __construct(FileRepository $files, $pathUploadPublic)
    {
        $this->files = $files;
        $this->pathUploadPublic = $pathUploadPublic;
    }

    public function getAll(?string $content, ?string $directory = null): array
    {
        $images = [];

        if (!$content) {
            return $images;
        }

        if ($directory) {
            $directory = '\/' . $directory . '\/';
        }

        $pattern = '/src=\"\/' . $this->pathUploadPublic . $directory . '(.*)\"/imU';

        preg_match_all($pattern, $content, $paths);

        foreach ($paths[1] as $filename) {

            if ($file = $this->files->findByFilename($filename)) {
                $images[] = $file;
            }
        }

        return $images;
    }

}
