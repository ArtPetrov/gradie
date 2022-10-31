<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\File\Entity;
use App\Model\File\Repository\FileTemporaryRepository;
use App\Model\File\UseCase\Category;
use App\Model\Cpanel\Entity\CategoryDealer;
use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\File\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Strings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @IsGranted("ROLE_ROOT")
 */
class FilesController extends AbstractController
{
    public const DIRECTORY_FILES = 'dealers';
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/files", name="files", methods={"GET"})
     */
    public function main(CategoryDealerRepository $categories, Request $request)
    {
        return $this->render('cpanel/files/main.html.twig', [
            'category_dealer' => $categories->findAllSortPosition(),

        ]);
    }

    /**
     * @Route("/files/category/{id}", name="files.category", methods={"GET"})
     */
    public function categoryFiles(CategoryDealer $category, Request $request)
    {
        $files = [];
        foreach ($category->getFiles()->getIterator() as $linkFile) {
            $files[] = $linkFile->getFile();
        }
        return $this->json($files, 200, []);
    }

    /**
     * @Route("/files/category/{id}/position", name="files.category.position", methods={"POST"})
     */
    public function categoryPosition(Request $request, Category\Position\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }

        $command = new Category\Position\Command($request->attributes->getInt('id'), $request->request->get('positions'));
        $handler->handle($command);

        return $this->json([], 200, []);
    }

    /**
     * @Route("/file/category", name="file.category.upload", methods={"POST","PUT"})
     */
    public function upload(Request $request, Uploader $uploader, EntityManagerInterface $em, ValidatorInterface $validator, Category\Add\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }

        $uploadedFile = $request->files->get('file');
        $category = (int)$request->request->get('category');


        $violations = $validator->validate(
            $uploadedFile, [
            new NotBlank(),
            new File(['maxSize' => '64M'])
        ]);

        if ($violations->count() > 0) {
            return $this->json($violations->get(0)->getMessage(), 400);
        }

        $file = $uploader->upload($uploadedFile, self::DIRECTORY_FILES, false);
        $command = new Category\Add\Command($file, $category);
        $handler->handle($command);
        return $this->json(['file' => $file, 'category' => $category], 201, []);
    }

    /**
     * @Route("/file/{file}/delete", name="file.delete", methods={"POST"})
     */
    public function delete(Entity\FileCategory $linkFile, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($linkFile);
        $em->flush();
        return new Response(null, 204);
    }

    /**
     * @Route("/file/{id}/rename", name="file.rename", methods={"POST"})
     */
    public function rename(Entity\File $file, Request $request, EntityManagerInterface $em, Uploader $uploader)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $file->setOriginalFilename($request->request->get('name', $file->getOriginalFilename()));
        $em->persist($file);
        $em->flush();
        return new Response(null, 204);
    }

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
     * @Route("/upload/tinyTemp", name="upload.tiny.temporary", methods={"POST","PUT"})
     */
    public function uploadTinyWithTemp(
        Request $request,
        FileTemporaryRepository $tmpFile,
        Uploader $uploader,
        EntityManagerInterface $em,
        string $pathUploadPublic,
        string $directoryForWYSIWYG
    ): Response
    {
        $uploadedFile = $request->files->get('file');
        $file = $uploader->upload($uploadedFile, $directoryForWYSIWYG, true);

        $em->persist($file);
        $em->flush();

        $tmpFile->add(new Entity\FileTemporary($file));
        $em->persist($file);
        $em->flush();

        return $this->json([
            'location' => '/' . $pathUploadPublic . '/' . $file->getPath()
        ], 201, []);
    }
}

