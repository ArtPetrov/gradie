<?php

declare(strict_types=1);

namespace App\Model\DesignProject\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class SizeType extends JsonType
{
    public const NAME = 'design.project.size';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof ArrayCollection) {
            throw new \DomainException("design.project.not.collection");
        }

        return json_encode($value->toArray());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new ArrayCollection(json_decode($value, TRUE));
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
