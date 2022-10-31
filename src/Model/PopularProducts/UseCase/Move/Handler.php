<?php

declare(strict_types=1);

namespace App\Model\PopularProducts\UseCase\Move;

use App\Model\Flusher;
use App\Model\PopularProducts\ReadModel\PopularFetcher;

class Handler
{
    private $flusher;
    private $populars;

    public function __construct(Flusher $flusher, PopularFetcher $populars)
    {
        $this->flusher = $flusher;
        $this->populars = $populars;
    }

    public function handle(Command $command): void
    {
        $product = $this->populars->get($command->id);
        $currentPosition = $product->getPosition();
        $friendlyId = $this->populars->findByPosition($currentPosition, $command->direction);

        if ($friendlyId === 0) {
            throw new \DomainException('position.extreme');
        }

        $friendlyWork = $this->populars->get($friendlyId);

        $product->setPosition($friendlyWork->getPosition());
        $friendlyWork->setPosition($currentPosition);
        $this->flusher->flush();
    }
}
