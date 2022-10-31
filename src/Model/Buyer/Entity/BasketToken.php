<?php

declare(strict_types=1);

namespace App\Model\Buyer\Entity;

use App\Helper\BasketTokenInterface;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class BasketToken implements BasketTokenInterface
{
    /**
     * @ORM\Column(type="guid", nullable=true, unique=true)
     */
    private $token;

    public function __construct(string $token)
    {
        Assert::notEmpty($token);
        Assert::uuid($token);
        $this->token = $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function isAvailable(): bool
    {
        return $this->token !== null;
    }
}
