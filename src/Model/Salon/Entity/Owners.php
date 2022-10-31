<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use App\Model\Dealer\Entity\Dealer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="salon_owners")
 */
class Owners
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Salon", inversedBy="owners")
     * @ORM\JoinColumn(name="salon_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $salon;

    /**
     * @var Dealer
     * @ORM\ManyToOne(targetEntity="App\Model\Dealer\Entity\Dealer", inversedBy="salons")
     * @ORM\JoinColumn(name="dealer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $dealer;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Salon $salon, Dealer $dealer)
    {
        $this->salon = $salon;
        $this->dealer = $dealer;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    public function getSalon(): Salon
    {
        return $this->salon;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function isCurrentDealer(Dealer $dealer): bool
    {
        return $this->dealer->getId() === $dealer->getId();
    }
}
