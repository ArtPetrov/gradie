<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Remove;

use App\Model\Flusher;
use App\Model\Promocode\Repository\PromocodeRepository;

class Handler
{
    private $promocodes;
    private $flusher;

    public function __construct(PromocodeRepository $promocodes, Flusher $flusher)
    {
        $this->promocodes = $promocodes;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $promo = $this->promocodes->get($command->id);
        $this->promocodes->remove($promo);
        $this->flusher->flush();
    }
}
