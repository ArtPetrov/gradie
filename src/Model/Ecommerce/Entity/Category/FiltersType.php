<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Category;

use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class FiltersType extends JsonType
{
    public const NAME = 'ecommerce.category.filters';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof ArrayCollection) {
            throw new \DomainException("product.category.not.collection");
        }

        $filters = [];

        /** @var Filter $attr */
        foreach ($value as $attr) {
            $filters[$attr->slug] = [
                'label' => $attr->label,
                'type' => $attr->type,
                'position' => $attr->position
            ];

        }
        return json_encode($filters);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $collection = new ArrayCollection();
        $attributes = json_decode($value, TRUE);
        if (count($attributes) > 0) {
            foreach ($attributes as $slug => $attr) {
                $collection->add(Filter::fromCategory(
                    $slug,
                    $attr['type'],
                    $attr['label'],
                    $attr['position']
                ));
            }
        }
        return ArrayCollectionHelper::sortableByField($collection, 'position');
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
