<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Category;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\MaterializedPathRepository")
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\Table(name="ecommerce_category",
 * indexes={
 *     @ORM\Index(name="path_idx", columns={"path"}),
 *     @ORM\Index(name="position_idx", columns={"position"}),
 *     @ORM\Index(name="filters_idx", columns={"filters"}),
 * })
 */
class Category implements EventBus
{
    use EventBusTrait;

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
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private $type;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Gedmo\TreePathSource
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\TreePath(separator="/", appendId=false, startsWithSeparator=true, endsWithSeparator=true)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\Embedded(class="Seo", columnPrefix="seo_")
     */
    private $seo;

    /**
     * @ORM\Column(type="ecommerce.category.filters", nullable=true, options={"jsonb":true})
     */
    private $filters;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(
        string $name,
        Seo $seo,
        Type $type,
        ArrayCollection $filters
    )
    {
        $this->name = $name;
        $this->type = $type;
        $this->seo = $seo;
        $this->filters = $filters;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getSeo(): Seo
    {
        return $this->seo;
    }

    public function updateSeo(Seo $seo): self
    {
        $this->seo = $seo;
        return $this;
    }

    public function setParent(Category $parent = null): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setPath($path): self
    {
        $this->path = $path;
        return $this->path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getFilters(): ArrayCollection
    {
        return $this->filters;
    }

    public function reloadFilters(ArrayCollection $filters): self
    {
        $this->filters = $filters;
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

    public function getChildrens()
    {
        return $this->children;
    }
}
