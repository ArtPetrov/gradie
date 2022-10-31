<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\File\Entity;
use App\Model\File\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Strings;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilesController extends AbstractController
{
    /**
     * @Route("/file/{id}", name="file", methods={"GET"})
     */
    public function download(Entity\File $file, Uploader $uploader)
    {
        $response = new StreamedResponse(static function () use ($file, $uploader) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploader->read($file);
            stream_copy_to_stream($fileStream, $outputStream);
        });

        $response->headers->set('Content-Type', $file->getMimeType());
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->getOriginalFilename(), Strings::toAscii($file->getFilename()));
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }


    /**
     * @Route("/file/upload", name="file.upload")
     */
    public function upload(Request $request, Uploader $uploader, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $uploadedFile = $request->files->get('form')['file'];

        $violations = $validator->validate(
            $uploadedFile,
            [
                new NotBlank([
                    'message' => 'Please select a file to upload'
                ]),
                new File([
                    'maxSize' => '64M',
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
            ]
        );

        if ($violations->count() > 0) {
            return $this->json($violations, 400);
        }

        $file = $uploader->upload($uploadedFile);

        $em->persist($file);
        $em->flush();

        return $this->json($file, 201, []);
    }

    /**
     * @Route("/file/{id}/delete", name="file.delete", methods={"GET"})
     */
    public function delete(Entity\File $file, EntityManagerInterface $em)
    {
        $em->remove($file);
        $em->flush();
        return new Response(null, 204);
    }
}