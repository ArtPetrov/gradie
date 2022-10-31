<?php

declare(strict_types=1);

namespace App\Model\Lead\UseCase\Remove;

use App\Model\Lead\Repository\LeadRepository;
use App\Model\Flusher;

class Handler
{
    private $leads;
    private $flusher;

    public function __construct(LeadRepository $leads, Flusher $flusher)
    {
        $this->leads = $leads;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $lead = $this->leads->get($command->id);
        $this->leads->remove($lead);
        $this->flusher->flush();
    }
}
