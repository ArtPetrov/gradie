<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Attribute;

use App\Model\Ecommerce\UseCase\Attribute\FieldValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class ValuesType extends JsonType
{
    public const NAME = 'ecommerce.attribute.values';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof ArrayCollection) {
            throw new \DomainException("attribute.fields.not.collection");
        }
        $values = [];
        foreach ($value as $val) {
            $values[] = $val;
        }
        return json_encode($values);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $collection = new ArrayCollection();
        $fields = json_decode($value, TRUE);
        if (count($fields) > 0) {
            foreach ($fields as $field) {
                $collection->add(FieldValue\Command::fromAttribute($field['label'], $field['value']));
            }
        }
        return $collection;
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
