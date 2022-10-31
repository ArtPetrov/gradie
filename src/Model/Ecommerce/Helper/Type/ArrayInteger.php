<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Helper\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ArrayInteger extends Type
{
    public const NAME = 'ecommerce.type.array.integer';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_array($value)) {
            throw new \DomainException("ecommerce.type.array.not.array");
        }
        $numbers = array_map(static function ($val) {
            return (int)$val;
        }, $value);
        return '{' . implode(",", $numbers) . '}';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return explode(',', trim($value, '{}'));
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'integer[]';
    }
}
