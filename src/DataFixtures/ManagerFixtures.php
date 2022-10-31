<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Cpanel\Entity\Administrator;
use App\Model\Cpanel\Entity\Manager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManagerFixtures extends BaseFixture
{
    public function loadData(ObjectManager $em)
    {
        $this->createMany(30, 'managers', function () {
            $manager = new Manager();
            $manager->setEmail($this->faker->email);
            $manager->setName($this->faker->firstName . ' ' . $this->faker->lastName);
            $manager->setPhone($this->faker->phoneNumber);
            return $manager;
        });

        $em->flush();
    }
}
