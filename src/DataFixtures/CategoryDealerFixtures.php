<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Cpanel\Entity\CategoryDealer;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryDealerFixtures extends BaseFixture
{
    public function loadData(ObjectManager $em)
    {
        $this->createMany(5, 'category_dealer', function ($i) {
            $category = new CategoryDealer();
            $category->setName('Категория ' . ++$i);
            return $category;
        });

        foreach ($this->getReferences('category_dealer') as $reference) {
            /** @var  CategoryDealer $reference */
            $reference->setPosition($reference->getId());
            $em->persist($reference);
        }

        $em->flush();
    }
}
