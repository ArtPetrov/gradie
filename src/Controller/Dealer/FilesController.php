<?php

declare(strict_types=1);

namespace App\Controller\Dealer;

use App\Model\File\Repository\FileCategoryRepository;
use App\Model\File\Service\Uploader;
use App\Model\File\Entity;
use Nette\Utils\Strings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController
{
    /**
     * @Route("/files", name="files")
     * @IsGranted("ROLE_DEALER")
     */
    public function files(FileCategoryRepository $fileCategoryRepository)
    {
        $listFile = $fileCategoryRepository->findBy(['category' => $this->getUser()->getCategory()], ['position' => 'DESC']);
        return $this->render('dealer/files/list.html.twig', [
            'files' => $listFile,
        ]);
    }

    /**
     * @Route("/file/{id}", name="file", methods={"GET"})
     * @IsGranted("DOWNLOAD", subject="file")
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

}
