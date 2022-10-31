<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Cpanel\Entity\Administrator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdministratorFixtures extends BaseFixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $em)
    {
        $this->createMany(1, 'administrators_me', function () {
            $admin = new Administrator();
            $admin->setEmail('artpetrov1@mail.ru');
            $admin->setName('Артём Петров');
            $admin->setPassword($this->passwordEncoder->encodePassword($admin, '123456'));
            return $admin;
        });

        $this->createMany(20, 'administrators', function () {
            $admin = new Administrator();
            $admin->setEmail($this->faker->email);
            $admin->setName($this->faker->firstName . ' ' . $this->faker->lastName);
            $admin->setPassword($this->passwordEncoder->encodePassword($admin, '123456'));
            return $admin;
        });

        $em->flush();
    }
}
