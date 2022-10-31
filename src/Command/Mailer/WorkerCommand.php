<?php

declare(strict_types=1);

namespace App\Command\Mailer;

use App\Model\Mailer\Repository\MailerRepository;
use App\Model\Mailer\Repository\RecipientRepository;
use App\Model\Mailer\Service\MailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WorkerCommand extends Command
{
    /**
     * @var MailerRepository
     */
    private $mailer;
    /**
     * @var RecipientRepository
     */
    private $recipients;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var MailSender
     */
    private $sender;

    public function __construct(MailerRepository $mailerRepository, RecipientRepository $recipients, EntityManagerInterface $em, MailSender $sender)
    {
        $this->mailer = $mailerRepository->activeMailer();
        $this->recipients = $recipients;
        $this->em = $em;

        parent::__construct();
        $this->sender = $sender;
    }

    protected function configure(): void
    {
        $this
            ->setName('mailer:worker')
            ->setDescription('Worker for sending email.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$this->mailer){
            return $output->writeln('<error>No active mailers.</error>');
        }

        $recipient = $this->recipients->recipientForMailer($this->mailer->getId());

        if(!$recipient){
            $output->writeln('<error>No recipients: mailer set status "closed".</error>');
            $this->mailer->process()->completed();
            $this->em->persist($this->mailer);
        }else {
            $this->sender->send($recipient->getEmail(), $this->mailer);
            $output->writeln(sprintf('<info>Mail for "%s" sent!</info>', $recipient->getEmail()));
            $recipient->sent();
            $this->em->persist($recipient);
        }

        $this->em->flush();
    }
}