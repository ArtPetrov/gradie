<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type1;

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
        $data['Стена C'] = $value->c;
        $data['Стена D'] = $value->d;
        $data['Стена E'] = $value->e;

        $data['Высота потолка'] = $value->hp;
        $data['Высота двери'] = $value->hd;
        $data['Ширина двери'] = $value->wd;

        return new ArrayCollection($data);
    }
}
