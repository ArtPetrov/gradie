<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type3;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class SizeTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        $data = [];

        $data['Стена А'] = $value->a;
        $data['Стена B'] = $value->b;

        $data['Высота потолка'] = $value->hp;


        return new ArrayCollection($data);
    }
}
