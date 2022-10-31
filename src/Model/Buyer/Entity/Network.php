<?php

declare(strict_types=1);

namespace App\Model\Buyer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="buyer_networks", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"network", "identity"})
 * })
 */
class Network
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Buyer", inversedBy="networks")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $buyer;
    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $network;
    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $identity;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Buyer $buyer, string $network, string $identity)
    {
        $this->buyer = $buyer;
        $this->network = $network;
        $this->identity = $identity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function isFor(string $network, string $identity): bool
    {
        return $this->network === $network && $this->identity === $identity;
    }

    public function isForNetwork(string $network): bool
    {
        return $this->network === $network;
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
