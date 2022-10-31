<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Edit;

use App\Model\Flusher;
use App\Model\Promocode\Entity\Information;
use App\Model\Promocode\Entity\Restrictions;
use App\Model\Promocode\Entity\Type;
use App\Model\Promocode\Repository\PromocodeRepository;

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
        $promo = $this->promocodes->get($command->id);

        if ($this->promocodes->hasCode($command->information->code)
            && $promo->getInformation()->getCode() !== mb_strtoupper($command->information->code)) {
            throw new \DomainException('promocode.code.not.unique');
        }

        $promo
            ->changeType(new Type($command->type))
            ->updateInformation(new Information($command->information->code, $command->information->name, $command->information->description))
            ->changeRestrictions(new Restrictions(
                $command->restrictions->countLimit,
                $command->restrictions->minSumOrder,
                $command->restrictions->maxSumOrder,
                $command->restrictions->dateStart,
                $command->restrictions->dateEnd
            ));

        $promo->editValue($command->value);

        if ($command->enable) {
            $promo->enable();
        } else {
            $promo->disable();
        }

        $this->flusher->flush($promo);
    }
}
