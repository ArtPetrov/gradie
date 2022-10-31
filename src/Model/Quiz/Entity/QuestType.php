<?php

declare(strict_types=1);

namespace App\Model\Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class QuestType
{
    public const INPUT = 'INPUT';
//    public const TEXTAREA = 'TEXTAREA';
    public const CHECKBOX = 'CHECKBOX';
    public const IMAGES_CHECKBOX = 'IMAGES_CHECKBOX';
    public const SELECT = 'SELECT';
//    public const OPTION = 'OPTION';
    public const IMAGES_OPTION = 'IMAGES_OPTION';

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $type;

    public function __construct(string $type)
    {
        Assert::oneOf($type, [
            self::INPUT,
//            self::TEXTAREA,
            self::CHECKBOX,
            self::SELECT,
//            self::OPTION,
            self::IMAGES_CHECKBOX,
            self::IMAGES_OPTION,
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isMedia(): bool
    {
        return $this->type === self::IMAGES_CHECKBOX || $this->type === self::IMAGES_OPTION;
    }

    public function isSupportVariable(): bool
    {
        //return $this->type === self::IMAGES_OPTION || $this->type === self::OPTION;
        return $this->type === self::IMAGES_OPTION;
    }

    public function getName(): ?string
    {
        return self::getTypes()[$this->getType()];
    }

    public static function getTypes(): array
    {
        return [
//            self::OPTION => "Радиосписок /\\",
            self::IMAGES_OPTION => "Выбор картинки /\\",
            self::INPUT => "Однострочные поля",
//            self::TEXTAREA => "Многострочные поля",
            self::CHECKBOX => "Группа чекбоксов",
            self::SELECT => "Раскрывающийся список",
            self::IMAGES_CHECKBOX => "Множественный выбор картинок",

        ];
    }
}
