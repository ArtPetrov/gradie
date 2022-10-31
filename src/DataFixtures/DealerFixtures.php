<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Dealer\Entity\Dealer;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DealerFixtures extends BaseFixture implements DependentFixtureInterface
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
        $this->createMany(100, 'dealer', function () {

            $dealer = Dealer::create($this->faker->email);
            $dealer->changePassword($this->passwordEncoder->encodePassword($dealer, '123456'));

            if ($this->faker->boolean()) {

                if ($this->faker->boolean(20)) {
                    $dealer->moderation()->block();
                } else {
                    $dealer->moderation()->activate();
                }

                if ($this->faker->boolean()) {
                    $dealer->assignManager($this->getRandomReference('managers'));
                }

                if ($this->faker->boolean()) {
                    $dealer->setCategory($this->getRandomReference('category_dealer'));
                }
            }

            if ($this->faker->boolean()) {
                $dealer->info()
                    ->changeName($this->faker->firstName . ' ' . $this->faker->lastName)
                    ->changePhone($this->faker->phoneNumber)
                    ->changeAddresss($this->faker->address)
                    ->changeInn($this->faker->numerify('############'))
                    ->changeKpp($this->faker->numerify('############'))
                    ->changeSite($this->faker->url);
            }
            return $dealer;
        });

        $em->flush();
    }


    public function getDependencies()
    {
        return [
            CategoryDealerFixtures::class,
            ManagerFixtures::class,
        ];
    }
}
