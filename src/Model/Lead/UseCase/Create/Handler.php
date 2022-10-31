<?php

declare(strict_types=1);

namespace App\Model\Lead\UseCase\Create;

use App\Model\Lead\Entity\Lead;
use App\Model\Lead\Entity\Client;
use App\Model\Lead\Entity\Type;
use App\Model\Flusher;
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
        $client = new Client($command->name, $command->phone, $command->email, $command->city);
        $type = new Type($command->type);

        $lead = Lead::create($client, $type);
        $this->leads->add($lead);

        $this->flusher->flush($lead);
    }
}

