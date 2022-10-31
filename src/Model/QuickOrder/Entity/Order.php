<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\Entity;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_quick")
 */
class Order implements EventBus
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
     * @var Client
     * @ORM\Embedded(class="Client", columnPrefix="client_")
     */
    private $client;

    /**
     * @var Manager
     * @ORM\Embedded(class="Manager", columnPrefix="manager_")
     */
    private $manager;

    /**
     * @var Product
     * @ORM\Embedded(class="Product", columnPrefix="product_")
     */
    private $product;

    /**
     * @var Status
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
        $this->manager = new Manager();
    }

    public static function create(Client $client, Product $product): self
    {
        return (new self())
            ->updateClient($client)
            ->changeStatus(new Status(Status::NEW))
            ->insertProduct($product);
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

    public function insertProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function changeStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getManagerComment(): ?string
    {
        return $this->manager->getComment();
    }

    public function updateManagerComment(?string $comment = null): self
    {
        $this->manager = new Manager($comment);
        return $this;
    }
}
