<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Regions;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public const TYPE = 'Regions';
    /**
     * @Assert\Length(max=64)
     */
    public $transportCompany;

    /**
     * @Assert\Length(max=64)
     */
    public $city;

    /**
     * @Assert\Length(max=255)
     */
    public $address;

    public $insurance;
    public $tightPacking;
    public $deliveryTo = 'company';

    public $baseCostShipping = 500;
    public $freeShippingLimit = 30000;

    public static function fromJson(array $fields): self
    {
        $command = new self();
        $command->transportCompany = $fields['transportCompany'];
        $command->address = $fields['address'];
        $command->city = $fields['city'];
        $command->deliveryTo = $fields['deliveryTo'];
        $command->insurance = (bool)$fields['insurance'];
        $command->tightPacking = (bool)$fields['tightPacking'];
        return $command;
    }
}
