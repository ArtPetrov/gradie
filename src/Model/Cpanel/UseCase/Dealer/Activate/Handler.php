<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Activate;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\Cpanel\Repository\ManagerRepository;
use App\Model\Cpanel\Service\CreateAccountSender;
use App\Model\Dealer\Repository\DealerRepository;
use App\Model\Dealer\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Handler
{
    /**
     * @var CategoryDealerRepository
     */
    private $categories;
    /**
     * @var ManagerRepository
     */
    private $managers;
    /**
     * @var DealerRepository
     */
    private $dealers;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var CreateAccountSender
     */
    private $sender;

    public function __construct(
        DealerRepository $dealers,
        CategoryDealerRepository $categories,
        ManagerRepository $managers,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder,
        CreateAccountSender $sender)
    {
        $this->categories = $categories;
        $this->managers = $managers;
        $this->dealers = $dealers;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        if ($command->category) {
            $command->dealer->setCategory($this->categories->findOneBy(['id' => $command->category]));
        }

        if ($command->manager) {
            $command->dealer->assignManager($this->managers->findOneBy(['id' => $command->manager]));
        }

        if ($command->generatePassword) {
            $password = PasswordGenerator::generate();
            $command->dealer->changePassword($this->passwordEncoder->encodePassword($command->dealer, $password));
        }

        $command->dealer->moderation()->activate();

        $this->em->persist($command->dealer);
        $this->em->flush();

        if ($command->sendMail) {
            $this->sender->send($command->dealer->getEmail(), $password, $command->dealer->info()->getName());
        }
    }
}
