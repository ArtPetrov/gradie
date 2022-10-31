<?php

declare(strict_types=1);

namespace App\Model\Lead\UseCase\Edit;

use App\Model\Flusher;
use App\Model\Lead\Entity\Status;
use App\Model\Lead\Repository\LeadRepository;

class Handler
{
    private $flusher;
    private $leads;

    public function __construct(Flusher $flusher, LeadRepository $leads)
    {
        $this->flusher = $flusher;
        $this->leads = $leads;
    }

    public function handle(Command $command): void
    {
        $lead = $this->leads->get($command->id);
        $lead->setStatus(new Status(Status::VIEW));
        $this->flusher->flush($lead);
    }
}

