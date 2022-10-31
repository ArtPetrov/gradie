<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\File\Service\Uploader;
use App\Model\File\Entity;
use App\Model\Article\Entity\Article;
use App\Model\Article\Entity\Image;
use App\Model\Article\Entity\Name;
use App\Model\Article\Entity\Seo;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ArticleFixture extends BaseFixture implements FixtureGroupInterface
{
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function loadData(ObjectManager $em)
    {
        $this->createMany(30, 'article', function ($i) {

            $date = new \DateTimeImmutable('-5 days');
            $date->modify('+' . $this->faker->numberBetween(1, 7) . 'days 3minutes');

            $seo = new Seo('title #' . $i, 'keywords #' . $i, 'description #' . $i);
            $name = new Name($this->faker->text(200), $this->faker->text(60));
            $news = Article::create($name, $this->faker->text(1000), $date, $seo);

            if ($this->faker->boolean(80)) {
                $news->addImage(new Image($news, $this->fakeUploadFiles(), true));
                foreach (range(0, $this->faker->numberBetween(0,15)) as $key) {
                    $news->addImage(new Image($news, $this->fakeUploadFiles(), false, (int) $key));
                }
            }
            return $news;
        });

        $em->flush();
    }

    private function randomFileName(): string
    {
        $files = glob(__DIR__ . '/Files/Images/' . '*.*');
        return basename($files[array_rand($files)]);
    }

    private function fakeUploadFiles(): Entity\File
    {
        $randomFileName = $this->randomFileName();
        $fs = new Filesystem();
        $targetPath = sys_get_temp_dir() . '/' . $randomFileName;
        $fs->copy(__DIR__ . '/Files/Images/' . $randomFileName, $targetPath, true);
        return $this->uploader->upload(new File($targetPath), Image::DIRECTORY_FILES, true);
    }

    public static function getGroups(): array
    {
        return ['content'];
    }
}
