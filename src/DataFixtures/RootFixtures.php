<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Cpanel\Entity\Administrator;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RootFixtures extends BaseFixture implements FixtureGroupInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $em)
    {
        $root = new Administrator();
        $root->setEmail('artpetrov1@mail.ru');
        $root->setPassword($this->passwordEncoder->encodePassword($root, '123456'));
        $em->persist($root);
        $em->flush();
    }

    public static function getGroups(): array
    {
        return ['content'];
    }
}
