<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use App\Model\Dealer\Entity\Dealer;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="salon_moderation")
 */
class ModerationSalon implements EventBus
{
    use EventBusTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Salon")
     * @ORM\JoinColumn(name="salon_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private $salon;

    /**
     * @var Dealer
     * @ORM\ManyToOne(targetEntity="App\Model\Dealer\Entity\Dealer")
     * @ORM\JoinColumn(name="dealer_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $dealer;

    /**
     * @ORM\Embedded(class="Coordinates", columnPrefix="coord_")
     */
    private $coordinates;

    /**
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private $type;

    /**
     * @ORM\Embedded(class="Status", columnPrefix=false)
     */
    private $status;

    /**
     * @ORM\Embedded(class="Information", columnPrefix="info_")
     */
    private $info;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public static function create(Salon $salonForModeration, Dealer $dealer, Coordinates $coords, Type $type, Information $info, ?string $comment = ''): self
    {
        $salon = new self();
        $salon
            ->changeStatus(new Status(Status::PROCESS))
            ->assignSalon($salonForModeration)
            ->assignDealer($dealer)
            ->updateCoords($coords)
            ->changeType($type)
            ->updateInfo($info)
            ->editComment($comment);
        return $salon;
    }

    public static function removeTicket(Salon $salonForModeration, Dealer $dealer,  Coordinates $coords, Type $type): self
    {
        $salon = new self();
        $salon
            ->changeStatus(new Status(Status::PROCESS_DELETE))
            ->assignSalon($salonForModeration)
            ->assignDealer($dealer)
            ->updateCoords($coords)
            ->changeType($type)
        ;
        return $salon;
    }

    public function assignDealer(Dealer $dealer): self
    {
        $this->dealer = $dealer;
        return $this;
    }

    public function changeStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function assignSalon(Salon $salon): self
    {
        $this->salon = $salon;
        return $this;
    }

    public function editComment(?string $comment = ''): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getSalon(): Salon
    {
        return $this->salon;
    }

    public function getCoords(): Coordinates
    {
        return $this->coordinates;
    }

    public function updateCoords(Coordinates $coords): self
    {
        $this->coordinates = $coords;
        return $this;
    }

    public function changeType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function updateInfo(Information $info): self
    {
        $this->info = $info;
        return $this;
    }

    public function getInfo(): Information
    {
        return $this->info;
    }
}
