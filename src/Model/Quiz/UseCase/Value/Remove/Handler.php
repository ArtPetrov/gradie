<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Value\Remove;

use App\Model\Flusher;
use App\Model\Quiz\Repository\ValueRepository;

class Handler
{
    private $values;
    private $flusher;

    public function __construct(ValueRepository $values, Flusher $flusher)
    {
        $this->values = $values;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $quest = $this->values->getById((int)$command->id);
        $this->values->remove($quest);
        $this->flusher->flush();
    }
}
