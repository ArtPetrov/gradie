<?php

declare(strict_types=1);

namespace App\Model\Lead\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="request_price",
 * indexes={
 *     @ORM\Index(name="request_price_idx", columns={"created_at"}),
 * })
 */
class Lead implements EventBus
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
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private $type;

    /**
     * @ORM\Embedded(class="Client", columnPrefix="client_")
     */
    private $client;

    /**
     * @ORM\Embedded(class="Status", columnPrefix=false)
     */
    private $status;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    private function __construct()
    {
        $this->status = new Status();
    }

    public static function create(Client $client, Type $type): self
    {
        $project = new self();
        $project->updateClient($client);
        $project->setType($type);
        return $project;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function updateClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
