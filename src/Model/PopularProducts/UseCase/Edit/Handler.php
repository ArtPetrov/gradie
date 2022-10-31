<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Edit;

use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use App\Model\PopularProducts\Entity\PopularProducts;
use App\Model\PopularProducts\Repository\PopularRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $flusher;
    private $populars;
    private $uploader;
    private $em;

    public function __construct(Flusher $flusher, EntityManagerInterface $em, PopularRepository $populars, Uploader $uploader)
    {
        $this->flusher = $flusher;
        $this->populars = $populars;
        $this->uploader = $uploader;
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $product = $this->populars->get($command->id);
        $product
            ->changeHeader($command->info->header)
            ->changeLink($command->info->link)
            ->updatePrice($command->info->price);

        if ($command->cover) {
            $file = $this->uploader->upload($command->cover, PopularProducts::DIRECTORY_FILES, true);
            $product->uploadCover($file);
            $this->em->remove($command->prevCover);
        }

        $this->flusher->flush($product);
    }
}