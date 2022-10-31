<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Results\Remove;

use App\Model\Flusher;
use App\Model\Quiz\Repository\ResultRepository;

class Handler
{
    private $results;
    private $flusher;

    public function __construct(ResultRepository $results, Flusher $flusher)
    {
        $this->results = $results;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $result = $this->results->getById((int)$command->id);
        $this->results->remove($result);
        $this->flusher->flush();
    }
}
