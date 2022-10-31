<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Image\Upload;

use App\Model\Gallery\Entity\Image;
use App\Model\File\Entity;
use App\Model\File\Repository\FileTemporaryRepository;
use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private $flusher;
    private $validator;
    private $uploader;
    private $tmpFiles;
    private $em;

    public function __construct(Flusher $flusher, ValidatorInterface $validator, Uploader $uploader, FileTemporaryRepository $tmpFiles, EntityManagerInterface $em)
    {
        $this->flusher = $flusher;
        $this->validator = $validator;
        $this->uploader = $uploader;
        $this->tmpFiles = $tmpFiles;
        $this->em = $em;
    }

    public function handle(Command $command): Entity\File
    {
        $violations = $this->validator->validate(
            $command->file, [
            new NotBlank(),
            new File([
                'maxSize' => '64M',
                'mimeTypes' => ['image/*']
            ])
        ]);

        if ($violations->count() > 0) {
            throw new \DomainException($violations->get(0)->getMessage());
        }

        $file = $this->uploader->upload($command->file, Image::DIRECTORY_FILES, true);
        $this->em->persist($file);
        $this->em->flush();

        $this->tmpFiles->add(new Entity\FileTemporary($file));
        $this->flusher->flush();

        return $file;
    }
}
