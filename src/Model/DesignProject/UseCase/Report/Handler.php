<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Report;

use App\Model\DesignProject\Repository\ProjectRepository;
use App\Model\DesignProject\Service\ReportService;
use App\Model\Flusher;
use Symfony\Component\HttpFoundation\Response;

class Handler
{
    private $flusher;
    private $projects;
    private $reportService;

    public function __construct(Flusher $flusher, ProjectRepository $projects, ReportService $reportService)
    {
        $this->flusher = $flusher;
        $this->projects = $projects;
        $this->reportService = $reportService;
    }

    public function handle(Command $command): Response
    {
        if ($command->dateStart > $command->dateEnd) {
            throw new \DomainException("design.projects.report.error.range");
        }

        $projects = $this->projects->getForRangeDate($command->dateStart, $command->dateEnd);

        if (0 === count($projects)) {
            throw new \DomainException("design.projects.report.error.zero");
        }

        $this->reportService->setDate($command->dateStart, $command->dateEnd);
        $this->reportService->appendProjects($projects);

        return $this->reportService->getFile();
    }
}
