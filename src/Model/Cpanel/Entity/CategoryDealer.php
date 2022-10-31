<?php

declare(strict_types=1);

namespace App\Model\Cpanel\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Cpanel\Repository\CategoryDealerRepository")
 */
class CategoryDealer
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var ArrayCollection[]|null
     * @ORM\OneToMany(targetEntity="App\Model\File\Entity\FileCategory", mappedBy="category", cascade={"remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id_category")
     * @ORM\OrderBy({"position" = "DESC"})
     */
    private $files;

    /**
     * @var ArrayCollection[]|null
     * @ORM\OneToMany(targetEntity="App\Model\Dealer\Entity\Dealer", mappedBy="category", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="category_dealer_id")
     */
    private $dealers;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $postion): self
    {
        $this->position = $postion;
        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getDealers(): ?Collection
    {
        return $this->dealers;
    }

}