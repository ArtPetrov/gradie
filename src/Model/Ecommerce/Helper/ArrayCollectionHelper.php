<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Helper;

use Doctrine\Common\Collections\ArrayCollection;

class ArrayCollectionHelper
{
    public static function equalValues(ArrayCollection $collection1, ArrayCollection $collection2): bool
    {
        return serialize($collection1->toArray()) === serialize($collection2->toArray());
    }

    public static function sortableByField(ArrayCollection $collection, string $fieldName): ArrayCollection
    {
        $array = $collection->toArray();

        usort($array, static function ($currentElement, $nextElement) use ($fieldName) {
            return $currentElement->$fieldName > $nextElement->$fieldName ? 1 : -1;
        });

        return new ArrayCollection($array);
    }
}