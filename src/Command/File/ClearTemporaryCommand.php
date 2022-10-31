<?php

declare(strict_types=1);

namespace App\Command\File;

use App\Model\File\Repository\FileRepository;
use App\Model\File\Repository\FileTemporaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearTemporaryCommand extends Command
{
    private $files;
    private $filesTemporary;
    private $em;

    public function __construct(FileRepository $files, FileTemporaryRepository $filesTemporary, EntityManagerInterface $em)
    {
        $this->files = $files;
        $this->filesTemporary = $filesTemporary;
        $this->em = $em;
        parent::__construct();

    }

    protected function configure(): void
    {
        $this
            ->setName('files:clear')
            ->setDescription('Clear temporary files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $temporaryFiles = $this->filesTemporary->findAllDaysAgo();

        foreach ($temporaryFiles as $tmp) {
            $file = $this->files->get((int)$tmp['file_id']);
            $this->em->remove($file);
            $output->writeln('<info>Remove file: ' . $file->getFilename() . '</info>');
        }

        $output->writeln('<info>Completed!</info>');
        $this->em->flush();
    }
}