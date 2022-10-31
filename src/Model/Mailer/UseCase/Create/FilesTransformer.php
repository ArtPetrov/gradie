<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use App\Model\File\Repository\FileRepository;
use Symfony\Component\Form\DataTransformerInterface;

class FilesTransformer implements DataTransformerInterface
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function transform($files)
    {
        if ($files->count() === 0) {
            return null;
        }

        $ids = [];
        foreach ($files as $file) {
            $ids[] = $file->getId;
        }

        return implode(',', $ids);
    }

    public function reverseTransform($files)
    {
        if (!$files) {
            return null;
        }
        return $this->fileRepository->getFilesById(explode(',', $files));
    }
}