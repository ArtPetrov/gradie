<?php

declare(strict_types=1);

namespace App\Model\Salon\Entity;

use App\Model\Dealer\Entity\Dealer;
use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="salon")
 */
class Salon implements EventBus
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
     * @ORM\Embedded(class="Coordinates", columnPrefix="coord_")
     */
    private $coordinates;

    /**
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private $type;

    /**
     * @ORM\Embedded(class="Information", columnPrefix="info_")
     */
    private $info;

    /**
     * @var ArrayCollection[]|null
     * @ORM\OneToMany(targetEntity="Owners", mappedBy="salon", orphanRemoval=true, cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="salon_id")
     */
    private $owners;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->owners = new ArrayCollection();
    }

    public static function create(Coordinates $coords, Type $type, Information $info): self
    {
        $salon = new self();
        $salon->updateCoords($coords);
        $salon->changeType($type);
        $salon->updateInfo($info);
        return $salon;
    }

    public function existOwner(Dealer $dealer): bool
    {
        foreach ($this->owners as $owner) {
            if ($owner->isCurrentDealer($dealer)) {
                return true;
            }
        }
        return false;
    }

    public function assignOwner(Dealer $dealer): self
    {
        if ($this->existOwner($dealer)) {
            return $this;
        }
        $this->owners->add(new Owners($this, $dealer));
        return $this;
    }

    public function removeOwner(Dealer $dealer): self
    {
        /** @var Owners $owner */
        foreach ($this->owners as $owner) {
            if ($owner->isCurrentDealer($dealer)) {
                $this->owners->removeElement($owner);
            }
        }
        return $this;
    }

    public function getOwners(): array
    {
        return $this->owners->toArray();
    }

    public function getOwner()
    {
        if ($this->owners->count() > 0) {
            return $this->owners->first();
        }
        return null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
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
