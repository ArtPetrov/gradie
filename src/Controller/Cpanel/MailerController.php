<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\File\Repository\FileRepository;
use App\Model\File\Service\Uploader;
use App\Model\File\Entity;
use App\Model\Mailer\Entity\Mailer;
use App\Model\Mailer\Repository\MailerRepository;
use App\Model\Mailer\UseCase\File\Delete;
use App\Model\Mailer\UseCase\File\Rename;
use App\Model\Ticket\Repository\TicketRepository;
use App\Model\Mailer\UseCase\Create;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nette\Utils\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MailerController extends AbstractController
{
    private const DIRECTORY_FILES = 'mailer';
    private const PER_PAGE = 10;

    /**
     * @var ErrorHandler
     */
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/mailers", name="mailers")
     */
    public function list(MailerRepository $mailers, Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $mailers->findBy([],['createdAt'=>'DESC']),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $this->render('cpanel/mailer/list.html.twig', [
            'mailers' => $pagination,
        ]);
    }

    /**
     * @Route("/mailer/create", name="mailer.create")
     */
    public function create(Request $request, Create\Handler $handler)
    {
        $command = new Create\Command($request->query->get('email', null));
        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'mailing.created');
                return $this->redirectToRoute('cpanel.mailers');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/mailer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mailer/ids", name="mailer.files", methods={"POST"})
     */
    public function files(Request $request, FileRepository $fileRepository)
    {
        return $this->json($fileRepository->getFilesById(explode(',', $request->request->get('ids', ''))), 200, []);
    }

    /**
     * @Route("/mailer/file/{id}/delete", name="mailer.file.delete", methods={"POST"})
     */
    public function fileDelete(Entity\File $file, Request $request, Delete\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $handler->handle(new Delete\Command($file));
        return new Response(null, 204);
    }

    /**
     * @Route("/mailer/file/{id}/rename", name="mailer.file.rename", methods={"POST"})
     */
    public function fileRename(Entity\File $file, Request $request, Rename\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Rename\Command($file,$request->request->get('name', $file->getOriginalFilename()));
        $handler->handle($command);
        return new Response(null, 204);
    }

    /**
     * @Route("/mailer/upload", name="mailer.upload", methods={"POST","PUT"})
     */
    public function upload(Request $request, Uploader $uploader, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $uploadedFile = $request->files->get('file');
        $violations = $validator->validate(
            $uploadedFile, [
            new NotBlank(),
            new File(['maxSize' => '64M'])
        ]);
        if ($violations->count() > 0) {
            return $this->json($violations->get(0)->getMessage(), 400);
        }
        $file = $uploader->upload($uploadedFile, self::DIRECTORY_FILES, false);
        $em->persist($file);
        $em->flush();
        return $this->json(['file' => $file], 201, []);
    }

    /**
     * @Route("/mailer/{id}/delete", name="mailer.delete")
     */
    public function delete(Mailer $mailer, Request $request,  EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $em->remove($mailer);
        $em->flush();
        return $this->json(null, 204);
    }

    /**
     * @Route("/mailer/{id}/run", name="mailer.run")
     */
    public function run(Mailer $mailer, Request $request,  EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $mailer->process()->run();
        $em->persist($mailer);
        $em->flush();
        return $this->redirectToRoute('cpanel.mailers');
    }

    /**
     * @Route("/mailer/{id}/stop", name="mailer.stop")
     */
    public function stop(Mailer $mailer, Request $request,  EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $mailer->process()->stop();
        $em->persist($mailer);
        $em->flush();
        return $this->redirectToRoute('cpanel.mailers');
    }

    /**
     * @Route("/mailer/file/{id}/download", name="mailer.file", methods={"GET"})
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
     * @Route("/mailer/{id}", name="mailer")
     */
    public function ticket(Mailer $mailer)
    {
        return $this->render('cpanel/mailer/view.html.twig', [
            'mailer' => $mailer
        ]);
    }


}
