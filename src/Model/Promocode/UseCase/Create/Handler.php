<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Create;

use App\Model\Flusher;
use App\Model\Promocode\Entity\Information;
use App\Model\Promocode\Entity\Restrictions;
use App\Model\Promocode\Entity\Promocode;
use App\Model\Promocode\Entity\Type;
use App\Model\Promocode\Repository\PromocodeRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

class Handler
{
    private $flusher;
    private $promocodes;

    public function __construct(Flusher $flusher, PromocodeRepository $promocodes)
    {
        $this->flusher = $flusher;
        $this->promocodes = $promocodes;
    }

    public function handle(Command $command): void
    {
        if ($this->promocodes->hasCode($command->information->code)) {
            throw new \DomainException('promocode.code.not.unique');
        }

        $type = new Type($command->type);
        $info = new Information($command->information->code, $command->information->name, $command->information->description);
        $restriction = new Restrictions(
            $command->restrictions->countLimit,
            $command->restrictions->minSumOrder,
            $command->restrictions->maxSumOrder,
            $command->restrictions->dateStart,
            $command->restrictions->dateEnd
        );
        $promo = Promocode::create($type, $command->value, $command->enable, $info, $restriction);
        $this->promocodes->add($promo);
        $this->flusher->flush($promo);
    }
}
