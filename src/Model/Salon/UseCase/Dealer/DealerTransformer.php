<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer;

use App\Model\Dealer\Entity\Dealer;
use App\Model\Dealer\ReadModel\DealerFetcher;
use Symfony\Component\Form\DataTransformerInterface;

class DealerTransformer implements DataTransformerInterface
{
    private $dealers;

    public function __construct(DealerFetcher $dealers)
    {
        $this->dealers = $dealers;
    }

    public function transform($value)
    {
        if (!$value) {
            return $value;
        }
        if (!$value->dealer instanceof Dealer) {
            return $value;
        }
        $value->id = $value->dealer->getId();
        $value->name = $value->dealer->getShowName();
        return $value;
    }

    public function reverseTransform($value)
    {
        if (empty($value->name)) {
            $value->id = null;
            $value->dealer = null;
        }

        $id = (int)$value->id;
        if (0 === $id) {
            return $value;
        }

        $value->dealer = $this->dealers->get($id);
        return $value;
    }
}
