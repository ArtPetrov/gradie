<?php

declare(strict_types=1);

namespace App\Model\Order\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Promocode
{
    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="json", nullable=true, options={"jsonb":true})
     */
    private $details;

    public static function createFromPromocode(\App\Model\Promocode\Entity\Promocode $promocode): self
    {
        $promo = new self();
        $promo->code = $promocode->getInformation()->getCode();
        $promo->details = json_encode([
            'id' => $promocode->getId(),
            'name' => $promocode->getInformation()->getName(),
            'type' => $promocode->getType()->getValue(),
            'value' => $promocode->getValue()
        ]);
        return $promo;
    }

    public static function createCopy(Promocode $promocode): self
    {
        $promo = new self();
        $promo->code = $promocode->getCode();
        $promo->details = json_encode($promocode->getDetails());
        return $promo;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getDetails(): ?array
    {
        return json_decode($this->details, true);
    }
}
