<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\File\Upload;

use App\Model\DesignProject\Entity\File;
use App\Model\File\Entity;
use App\Model\File\Repository\FileTemporaryRepository;
use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\File as ConstraintFile;
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
            new ConstraintFile([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/*',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain'
                ]
            ])
        ]);

        if ($violations->count() > 0) {
            throw new \DomainException($violations->get(0)->getMessage());
        }

        $file = $this->uploader->upload($command->file, File::DIRECTORY_FILES, false);
        $this->em->persist($file);
        $this->em->flush();

        $this->tmpFiles->add(new Entity\FileTemporary($file));
        $this->flusher->flush();

        return $file;
    }
}
