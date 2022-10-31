<?php

declare(strict_types=1);

namespace App\Command\Administrator;

use App\Model\Cpanel\UseCase\Administrator\Create;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateCommand extends Command
{
    private $command;
    private $handler;

    public function __construct(Create\Handler $handler, Create\Command $command)
    {
        $this->handler = $handler;
        $this->command = $command;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('administrator:create')
            ->setDescription('Create a new administrator.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $this->command->email = $helper->ask($input, $output, new Question('Email: '));
        $this->command->password = $helper->ask($input, $output, (new Question('Password: '))->setHidden(true)->setHiddenFallback(false));
        $this->command->repeatPassword = $helper->ask($input, $output, (new Question('Repeat password: '))->setHidden(true)->setHiddenFallback(false));
        $this->handler->handle($this->command);

        $output->writeln('<info>Administrator create!</info>');
    }
}