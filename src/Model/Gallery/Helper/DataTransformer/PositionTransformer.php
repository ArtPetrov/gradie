<?php

declare(strict_types=1);

namespace App\Model\Gallery\Helper\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class PositionTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        if (!$value instanceof ArrayCollection) {
            return new ArrayCollection();
        }

        if (0 === $value->count()) {
            return $value;
        }

        $index = 0;
        foreach ($value as $key => $element) {
            $value->get($key)->position = $index;
            $index++;
        }
        return $value;
    }
}
