<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use App\Model\Ecommerce\Helper\ArrayCollectionHelper;
use App\Model\Ecommerce\Entity\Attribute\Field;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class AttributesType extends JsonType
{
    public const NAME = 'ecommerce.product.attributes';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof ArrayCollection) {
            throw new \DomainException("product.attributes.not.collection");
        }

        $attributes = [];

        foreach ($value as $attr) {
            $attributes[$attr->slug] = [
                'value' => Field::convertToType($attr->type, $attr->value),
                'label' => (string)$attr->label,
                'visible' => (bool)$attr->visible,
                'type' => (string)$attr->type,
                'position' => (int)$attr->position
            ];

        }
        return json_encode($attributes);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $collection = new ArrayCollection();
        $attributes = json_decode($value, TRUE);
        if (count($attributes) > 0) {
            foreach ($attributes as $slug => $attr) {
                $collection->add(Attribute::fromDatabase($slug, $attr));
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
