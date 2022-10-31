<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\File\Service\Uploader;
use App\Model\File\Entity;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class DealerFilesFixture extends BaseFixture implements DependentFixtureInterface
{
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function loadData(ObjectManager $em)
    {
        $this->createMany(30, 'file_category', function ($i) {
            $fileCategory = new Entity\FileCategory();
            $fileCategory->setFile($this->fakeUploadFiles());
            $fileCategory->setCategory($this->getRandomReference('category_dealer'));
            $fileCategory->setPosition($i);
            return $fileCategory;
        });

        $em->flush();
    }

    private function randomFileName(): string
    {
        $files = glob(__DIR__ . '/Files/Dealers/' . '*.*');
        return basename($files[array_rand($files)]);
    }

    private function fakeUploadFiles(): Entity\File
    {
        $randomFileName = $this->randomFileName();

        $fs = new Filesystem();
        $targetPath = sys_get_temp_dir() . '/' . $randomFileName;
        $fs->copy(__DIR__ . '/Files/Dealers/' . $randomFileName, $targetPath, true);
        return $this->uploader->upload(new File($targetPath), 'dealers', false);
    }

    public function getDependencies()
    {
        return [
            CategoryDealerFixtures::class,
        ];
    }
}
