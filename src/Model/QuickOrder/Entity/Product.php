<?php

declare(strict_types=1);

namespace App\Model\QuickOrder\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $article;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    public function __construct(int $id, string $article, string $name, int $count)
    {
        $this->id = $id;
        $this->article = $article;
        $this->name = $name;
        $this->count = $count;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArticle(): string
    {
        return $this->article;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
