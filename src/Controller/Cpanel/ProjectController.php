<?php

declare(strict_types=1);

namespace App\Controller\Cpanel;

use App\Controller\ErrorHandler;
use App\Model\DesignProject\Entity\Project;
use App\Model\DesignProject\Repository\ProjectRepository;
use App\Model\DesignProject\UseCase\Remove;
use App\Model\DesignProject\UseCase\Report;
use App\Model\DesignProject\UseCase\Edit;
use App\Model\File\Service\Uploader;
use App\Model\File\Entity;
use Knp\Component\Pager\PaginatorInterface;
use Nette\Utils\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private const PER_PAGE = 15;
    private $errors;

    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/design-projects", name="design.projects")
     */
    public function list(Request $request, ProjectRepository $projects, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $projects->getAll(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        $report = $this->createForm(Report\Form::class, new Report\Command(), [
            'action' => $this->generateUrl('design.projects.report'),
            'method' => 'POST',
        ]);

        return $this->render('cpanel/projects/list.html.twig', [
            'projects' => $pagination,
            'form' => $report->createView()
        ]);
    }

    /**
     * @Route("/design-projects/report", name="design.projects.report", methods={"POST"})
     */
    public function report(Request $request, Report\Handler $handler)
    {
        $command = new Report\Command();
        $report = $this->createForm(Report\Form::class, $command);
        $report->handleRequest($request);
        if ($report->isSubmitted() && $report->isValid()) {
            try {
                return $handler->handle($command);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('cpanel.design.projects');
    }

    /**
     * @Route("/design-project/{id}", name="design.project", methods={"POST","GET"})
     */
    public function edit(Project $project, Request $request, Edit\Handler $handler)
    {
        $command = Edit\Command::fromProject($project);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'project.updated');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('cpanel/projects/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/design-project/{id}/remove", name="design.project.remove")
     */
    public function delete(Project $project, Request $request, Remove\Handler $handler)
    {
        if (!$this->isCsrfTokenValid('any', $request->request->get('token_csrf'))) {
            return $this->json(null, 401);
        }
        $command = new Remove\Command($project->getId());
        $handler->handle($command);
        return $this->json(null, 204);
    }


    /**
     * @Route("/design-project/file/{id}", name="design.project.file", methods={"GET"})
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
