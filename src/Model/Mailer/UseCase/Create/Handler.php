<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\Entity\Status;
use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Mailer\Entity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var DealerRepository
     */
    private $dealerRepository;
    /**
     * @var CategoryDealerRepository
     */
    private $categoryDealerRepository;

    public function __construct(EntityManagerInterface $em, DealerRepository $dealerRepository, CategoryDealerRepository $categoryDealerRepository)
    {
        $this->em = $em;
        $this->dealerRepository = $dealerRepository;
        $this->categoryDealerRepository = $categoryDealerRepository;
    }

    public function handle(Command $command): void
    {
        $sender = new Entity\Sender($command->sender->email, $command->sender->name);

        $mail = new Entity\Mail($command->mail->header, $command->mail->content);

        if ($command->mail->files) {
            foreach ($command->mail->files as $file) {
                $mail->attachFile(new Entity\File($file));
            }
        }

        $mailer = new Entity\Mailer($command->name, $sender, $mail);

        if ($command->type === Entity\Mailer::TYPE_MAIL) {
            $mailer->setTypeMail();
        } else {
            $mailer->setTypeMailing();
        }

        switch ($command->recipient->type) {
            case Recipient::TYPE_CATEGORY:
                $this->recipientsCategory($mailer, $command->recipient->categories);
                break;
            case Recipient::TYPE_EMAIL:
                $this->recipientsEmail($mailer, $command->recipient->emails);
                break;
            case Recipient::TYPE_ALL:
                $this->recipientsAll($mailer);
                break;
            default:
                throw new \DomainException('mailer.select.type.recipient');
        }

        $this->em->persist($mailer);
        $this->em->flush();
    }

    private function recipientsAll(Entity\Mailer $mailer): void
    {
        if ($mailer->isMailType()) {
            $criteria = ['status.status' => Status::STATUS_ACTIVE];
        } else {
            $criteria = ['mailer.approve' => true, 'status.status' => Status::STATUS_ACTIVE];
        }

        $dealers = $this->dealerRepository->findBy($criteria);

        /** @var Dealer $dealer */
        foreach ($dealers as $dealer) {
            $mailer->addRecipient(new Entity\Recipient($dealer->getEmail()));
        }
    }

    private function recipientsEmail(Entity\Mailer $mailer, ?string $emailsPlain): void
    {
        if (!$emailsPlain) {
            throw new \DomainException('mailer.recipient.email.null');
        }

        $emails = explode(',', $emailsPlain);

        foreach ($emails as $email) {
            $clearEmail = trim($email);
            if (strlen($clearEmail) === 0) {
                continue;
            }
            $mailer->addRecipient(new Entity\Recipient($clearEmail));
        }

        if ($mailer->recipients()->count() === 0) {
            throw new \DomainException('mailer.recipient.email.null');
        }
    }

    private function recipientsCategory(Entity\Mailer $mailer, Collection $categories): void
    {
        if ($categories->count() === 0) {
            throw new \DomainException('mailer.recipient.categories.null');
        }

        foreach ($categories as $category) {
            if ($category->getDealers()->count() > 0) {
                /** @var Dealer $dealer */
                foreach ($category->getDealers() as $dealer) {

                    if (!$dealer->notification()->approve() && $mailer->isMailingType()) {
                        continue;
                    }
                    $mailer->addRecipient(new Entity\Recipient($dealer->getEmail()));
                }
            }
        }

        if ($mailer->recipients()->count() === 0) {
            throw new \DomainException('mailer.recipient.categories.not.dealers');
        }
    }
}
