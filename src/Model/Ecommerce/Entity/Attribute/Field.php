<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Attribute;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Field
{
    public const NUMBER = 'NUMBER';
    public const TEXT = 'TEXT';
    public const SELECT = 'SELECT';
    public const BOOL = 'BOOL';
    public const CHECKBOX = 'CHECKBOX';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, [
            self::NUMBER,
            self::TEXT,
            self::SELECT,
            self::BOOL,
            self::CHECKBOX,
        ]);

        $this->type = $type;
    }

    public function getTypeAttribute(): string
    {
        return $this->type;
    }

    public static function allTypesFields(): array
    {
        return [
            'Числовое поле' => self::NUMBER,
            'Текстовое поле' => self::TEXT,
            'Поле с выбором варианта' => self::SELECT,
            'Логическое поле' => self::BOOL,
            'Множественный выбор' => self::CHECKBOX,
        ];
    }

    public static function convertToType(string $type, $value)
    {
        if ($type === self::NUMBER) {
            return (float)$value;
        }
        if ($type === self::BOOL) {
            return $value==='true';
        }
        return (string)$value;
    }

    public static function labelField(string $typeField): string
    {
        return array_flip(self::allTypesFields())[$typeField];
    }

    public function getNameTypeFields(): string
    {
        return array_search($this->type, self::allTypesFields());
    }

}
