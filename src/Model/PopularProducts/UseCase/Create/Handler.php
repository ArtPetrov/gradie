<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Create;

use App\Model\File\Service\Uploader;
use App\Model\Flusher;
use App\Model\PopularProducts\Entity\PopularProducts;
use App\Model\PopularProducts\Repository\PopularRepository;

class Handler
{
    private $flusher;
    private $populars;
    private $uploader;

    public function __construct(Flusher $flusher, PopularRepository $populars, Uploader $uploader)
    {
        $this->flusher = $flusher;
        $this->populars = $populars;
        $this->uploader = $uploader;
    }

    public function handle(Command $command): void
    {
        $product = PopularProducts::create($command->info->header, $command->info->link, $command->info->price,);

        if (!$command->cover) {
            throw new \DomainException('cover.not.upload');
        }

        $file = $this->uploader->upload($command->cover, PopularProducts::DIRECTORY_FILES, true);
        $product->uploadCover($file);

        $this->populars->add($product);
        $this->flusher->flush($product);
    }
}

