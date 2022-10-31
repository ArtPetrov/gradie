<?php

declare(strict_types=1);

namespace App\Model\Ticket\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const TYPE_QUESTION = 'question';
    public const TYPE_ANSWER = 'answer';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    public function __construct(string $type='answer')
    {
        Assert::oneOf($type, [
            self::TYPE_QUESTION,
            self::TYPE_ANSWER
        ]);

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function question(): self
    {
        $this->type = self::TYPE_QUESTION;
        return $this;
    }

    public function answer(): self
    {
        $this->type = self::TYPE_ANSWER;
        return $this;
    }

}
