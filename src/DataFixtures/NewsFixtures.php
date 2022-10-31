<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\News\Entity\News;
use Doctrine\Common\Persistence\ObjectManager;

class NewsFixtures extends BaseFixture
{
    public function loadData(ObjectManager $em)
    {

        $this->createMany(20, 'news', function () {
            $news = new News();
            $date = new \DateTimeImmutable('-5 days');
            $date->modify('+' . $this->faker->numberBetween(1, 7) . 'days 3minutes');
            $news->setContent($this->faker->paragraph(mt_rand(10, 30)));
            $news->setHeader($this->faker->text(240));
            $news->setPublishedAt($date);
            return $news;
        });

        $em->flush();
    }
}
