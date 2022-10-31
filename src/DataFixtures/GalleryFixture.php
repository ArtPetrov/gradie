<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\File\Service\Uploader;
use App\Model\File\Entity;
use App\Model\Gallery\Entity\Album;
use App\Model\Gallery\Entity\Image;
use App\Model\Gallery\Entity\Name;
use App\Model\Gallery\Entity\Seo;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class GalleryFixture extends BaseFixture implements FixtureGroupInterface
{
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function loadData(ObjectManager $em)
    {
        $this->createMany(30, 'gallery', function ($i) {

            $seo = new Seo('title #'.$i,'keywords #'.$i, 'description #'.$i);
            $name = new Name($this->faker->text(200),$this->faker->text(60));
            $album = Album::create($name,$seo);
            $album->addImage(new Image($album,$this->fakeUploadFiles(),true));
            foreach (range(0, $this->faker->numberBetween(0,15)) as $key) {
                $album->addImage(new Image($album, $this->fakeUploadFiles(), false, (int) $key));
            }
            return $album;
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
