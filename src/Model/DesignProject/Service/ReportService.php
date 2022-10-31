<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Service;

use App\Model\DesignProject\Entity\Project;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\RouterInterface;


class ReportService
{
    private $filename;
    private $spreadsheet;
    private $projects = [];
    private $dateSign;
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->setHeaderInfo();
        $this->setStyleColumnsHeader();
        $this->setNameColumns();
        $this->router = $router;
    }

    private function setHeaderInfo(): void
    {
        $this->spreadsheet->getProperties()->setCreator('Gardie-design.com')
            ->setLastModifiedBy('Gardie-design.com')
            ->setKeywords('Gardie Design')
            ->setCategory('Reports');
    }

    public function setDate(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        $this->dateSign = $start->format('d.m.Y') . ' - ' . $end->format('d.m.Y');
    }

    public function appendProjects(array $projects): void
    {
        $this->projects = array_merge($this->projects, $projects);
    }

    public function getFileName(): string
    {
        return 'Отчет по дизайн-проектам за ' . $this->dateSign . '.xlsx';
    }

    private function setStyleColumnsHeader()
    {
        foreach (range('A', 'I') as $letter) {
            $this->spreadsheet->getActiveSheet()->getColumnDimension($letter)->setAutoSize(true);
            $this->spreadsheet->getActiveSheet()->getStyle($letter)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $this->spreadsheet->getActiveSheet()->getStyle($letter . '1')->getFont()->setBold(true);

        }
    }

    private function setNameColumns()
    {
        $this->spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Дата')
            ->setCellValue('C1', 'Статус')
            ->setCellValue('D1', 'Тип помещения')
            ->setCellValue('E1', 'Город')
            ->setCellValue('F1', 'ФИО')
            ->setCellValue('G1', 'Телефон')
            ->setCellValue('H1', 'Почта')
            ->setCellValue('I1', 'Вид дизайн-проекта');
    }

    private function fillData()
    {
        $i = 1;
        /** @var Project $project */
        foreach ($this->projects as $project) {
            $i++;
            $this->spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $project->getId())
                ->setCellValue('B' . $i, $project->getCreatedAt()->format('d.m.Y H:i:s'))
                ->setCellValue('C' . $i, $project->getStatus()->getLabel())
                ->setCellValue('D' . $i, $project->getType()->getType())
                ->setCellValue('E' . $i, $project->getClient()->getCity())
                ->setCellValue('F' . $i, $project->getClient()->getName())
                ->setCellValue('G' . $i, $project->getClient()->getPhone())
                ->setCellValue('H' . $i, $project->getClient()->getEmail())
                ->setCellValue('I' . $i, $project->getInfo()->getName());
        }
    }

    public function getFilePathTemp(): ?string
    {
        $writer = new Xlsx($this->spreadsheet);
        $fileLocation = tempnam(sys_get_temp_dir(), $this->getFileName());
        $writer->save($fileLocation);
        return $fileLocation;
    }

    public function getFile(): Response
    {
        $this->fillData();
        $this->spreadsheet->getActiveSheet()->setTitle('Отчет по дизайн-проектам');
        $this->spreadsheet->setActiveSheetIndex(0);

        $response = new BinaryFileResponse($this->getFilePathTemp());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $this->getFileName());
        return $response;
    }

}
