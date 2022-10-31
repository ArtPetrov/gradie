<?php

declare(strict_types=1);

namespace App\Model\File\Service;

use App\Model\File\Entity;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var RequestStackContext
     */
    private $requestStackContext;

    /**
     * @var FilesystemInterface
     */
    private $privateFilesystem;

    public function __construct(FilesystemInterface $publicFilesystem, FilesystemInterface $privateFilesystem, RequestStackContext $requestStackContext)
    {
        $this->filesystem = $publicFilesystem;
        $this->requestStackContext = $requestStackContext;
        $this->privateFilesystem = $privateFilesystem;

    }

    public function getFilesystem(bool $isPublic = true): FilesystemInterface
    {
        return $isPublic ? $this->filesystem : $this->privateFilesystem;
    }

    public function upload(File $file, string $directoryName = 'others', bool $isPublic = true): Entity\File
    {
        $originalFilename = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename();
        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->guessExtension();

        $stream = fopen($file->getPathname(), 'r');

        $this->getFilesystem($isPublic)
            ->write($directoryName . DIRECTORY_SEPARATOR . $newFilename, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        $uploadedFile = new Entity\File();
        $uploadedFile->setDirectory($directoryName);
        $uploadedFile->setOriginalFilename($originalFilename);
        $uploadedFile->setFilename($newFilename);
        $uploadedFile->setMimeType($file->getMimeType() ?? 'application/octet-stream');
        $uploadedFile->setSize($file->getSize());
        $uploadedFile->setExtension($file->getExtension());

        if (!$isPublic) {
            $uploadedFile->makePrivate();
        }

        return $uploadedFile;
    }

    public function read(Entity\File $file)
    {
        $resource = $this->getFilesystem($file->isPublic())
            ->readStream($file->getPath());

        if ($resource === false) {
            throw new \Exception('error.opening.stream');
        }

        return $resource;
    }

    public function delete(Entity\File $file): bool
    {
        try {
            $result = $this->getFilesystem($file->isPublic())->delete($file->getPath());
            if ($result === false) {
                throw new \Exception(sprintf('Error deleting "%s"', $file->getPath()));
            }
        } catch (FileNotFoundException $e) {
            $result = true;
        }
        return $result;
    }
}