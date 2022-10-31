<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Remove;

use App\Model\Ecommerce\Repository\GroupRepository;
use App\Model\Flusher;

class Handler
{
    private $groups;
    private $flusher;

    public function __construct(GroupRepository $groups, Flusher $flusher)
    {
        $this->groups = $groups;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $group = $this->groups->get($command->id);
        $this->groups->remove($group);
        $this->flusher->flush();
    }
}
