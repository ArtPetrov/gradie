<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Administrator\Edit;

use App\Model\Cpanel\Repository\AdministratorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Handler
{
    private $administrators;
    private $em;
    private $passwordEncoder;

    public function __construct(AdministratorRepository $administrators, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->administrators = $administrators;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function handle(Command $command): void
    {
        $administrator = $this->administrators->findOneBy(['id' => $command->id]);

        $administrator->setName($command->name);

        if ($this->administrators->duplicateEmail($command->email, $command->id)) {
            throw new \DomainException('email.duplicate');
        }

        $administrator->setEmail($command->email);

        if ($command->password) {
            if ($command->password !== $command->repeatPassword) {
                throw new \DomainException('passwords.not.equal');
            }
            $administrator->setPassword($this->passwordEncoder->encodePassword($administrator, $command->password));
        }

        $this->em->persist($administrator);
        $this->em->flush();
    }
}
