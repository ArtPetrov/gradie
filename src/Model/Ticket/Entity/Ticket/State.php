<?php

declare(strict_types=1);

namespace App\Model\Ticket\Entity\Ticket;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class State
{
    public const SUPPORT_READ = 'support_read';
    public const SUPPORT_REPLY = 'support_reply';
    public const SUPPORT_CLOSED = 'support_closed';
    public const AUTHOR_READ = 'author_read';
    public const AUTHOR_ASKED = 'author_asked';
    public const AUTHOR_CLOSED = 'author_closed';

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $state;

    public function __construct(string $state = 'author_asked')
    {
        Assert::oneOf($state, [
            self::SUPPORT_READ,
            self::SUPPORT_REPLY,
            self::SUPPORT_CLOSED,
            self::AUTHOR_CLOSED,
            self::AUTHOR_READ,
            self::AUTHOR_ASKED
        ]);

        $this->state = $state;
    }

    public function isNewForAuthor(): bool
    {
        return $this->state === self::SUPPORT_REPLY;
    }

    public function isNewForSupport(): bool
    {
        return $this->state === self::AUTHOR_ASKED;
    }

    public function authorRead(): self
    {
        $this->state = self::AUTHOR_READ;
        return $this;
    }

    public function authorAsked(): self
    {
        $this->state = self::AUTHOR_ASKED;
        return $this;
    }

    public function authorClosed(): self
    {
        $this->state = self::AUTHOR_CLOSED;
        return $this;
    }

    public function supportRead(): self
    {
        $this->state = self::SUPPORT_READ;
        return $this;
    }

    public function supportReply(): self
    {
        $this->state = self::SUPPORT_REPLY;
        return $this;
    }

    public function supportClosed(): self
    {
        $this->state = self::SUPPORT_CLOSED;
        return $this;
    }

}
