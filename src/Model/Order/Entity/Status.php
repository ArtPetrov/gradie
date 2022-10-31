<?php

declare(strict_types=1);

namespace App\Model\Order\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Status
{
    public const CLIENT_ENTERED_CONTACT = 'CLIENT_ENTERED_CONTACT';
    public const CLIENT_ENTERED_ADDRESS = 'CLIENT_ENTERED_ADDRESS';
    public const CLIENT_CHOSE_HELP_MANAGER = 'CLIENT_CHOSE_HELP_MANAGER';
    public const CLIENT_REFUSED_HELP = 'CLIENT_REFUSED_HELP';
    public const PENDING_PAYMENT = 'PENDING_PAYMENT';
    public const IN_PROCESSING = 'IN_PROCESSING';
    public const CANCELED = 'CANCELED';
    public const COMPLETED = 'COMPLETED';
    public const IN_WORK = 'IN_WORK';
    public const CANCELED_CLIENT = 'CANCELED_CLIENT';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $status;

    public function __construct(string $status = 'CLIENT_ENTERED_CONTACT')
    {
        Assert::oneOf($status, [
            self::CLIENT_ENTERED_CONTACT,
            self::CLIENT_ENTERED_ADDRESS,
            self::CLIENT_CHOSE_HELP_MANAGER,
            self::CLIENT_REFUSED_HELP,
            self::PENDING_PAYMENT,
            self::IN_PROCESSING,
            self::CANCELED,
            self::COMPLETED,
            self::IN_WORK,
            self::CANCELED_CLIENT,
        ]);

        $this->status = $status;
    }

    public function getValue(): string
    {
        return $this->status;
    }

    public function inProcessCreating(): bool
    {
        return (bool)in_array($this->status, [
            self::CLIENT_ENTERED_CONTACT,
            self::CLIENT_ENTERED_ADDRESS,
            self::CLIENT_CHOSE_HELP_MANAGER,
            self::CLIENT_REFUSED_HELP
        ]);
    }

    public static function getStatusForFilter(): array
    {
        return [
            'Указали контактные данные' => self::CLIENT_ENTERED_CONTACT,
            'Ввели адрес доставки' => self::CLIENT_ENTERED_ADDRESS,
            'Ожидает помощи менеджера' => self::CLIENT_CHOSE_HELP_MANAGER,
            'Отказались от помощи' => self::CLIENT_REFUSED_HELP,
            'Ожидает оплаты' => self::PENDING_PAYMENT,
            'Поступил в работу' => self::IN_PROCESSING,
            'В работе(обработан)' => self::IN_WORK,
            'Отменен' => self::CANCELED,
            'Отменен клиентом' => self::CANCELED_CLIENT,
            'Выполнен' => self::COMPLETED,
        ];
    }

    public function availableForManager(): bool
    {
        return array_key_exists($this->getValue(), array_flip(self::getStatusForManager()));
    }

    public function getName(): ?string
    {
        return array_flip(self::getStatusForFilter())[$this->getValue()];
    }

    public static function getStatusForManager(): array
    {
        return [
            'Поступил в работу' => self::IN_PROCESSING,
            'В работе(обработан)' => self::IN_WORK,
            'Отменен клиентом' => self::CANCELED_CLIENT,
            'Отменен' => self::CANCELED,
            'Выполнен' => self::COMPLETED,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->getValue() === self::COMPLETED;
    }

    public function isCanceled(): bool
    {
        return $this->getValue() === self::CANCELED;
    }

    public function inProcessing(): bool
    {
        return $this->getValue() === self::PENDING_PAYMENT ||
            $this->getValue() === self::IN_PROCESSING;
    }

    public function canBeClosed(): bool
    {
        return $this->getValue() !== self::CANCELED_CLIENT &&
            $this->getValue() !== self::CANCELED &&
            $this->getValue() !== self::COMPLETED;
    }

    public static function getStatusForShowClient(): array
    {
        return [
            self::CANCELED_CLIENT,
            self::CANCELED,
            self::IN_WORK,
            self::COMPLETED,
            self::IN_PROCESSING,
            self::PENDING_PAYMENT
        ];
    }
}
