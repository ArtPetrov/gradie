<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\Entity\Product;

use App\Model\EventBus;
use App\Model\EventBusTrait;
use App\Model\File\Entity\File;
use App\Model\Review\Entity\Review;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ecommerce_product",
 * indexes={
 *     @ORM\Index(name="enable_popular_idx", columns={"enable","popular"}, options={"where": "(enable = true)"}),
 *     @ORM\Index(name="enable_idx", columns={"enable"}, options={"where": "(enable = true)"}),
 *     @ORM\Index(name="serach_for_kit_idx", columns={"info_name", "info_article","price_final"}, options={"where": "(price_final = true)"}),
 *     @ORM\Index(name="article_idx", columns={"info_article"}),
 *     @ORM\Index(name="attributes_idx", columns={"attributes"}),
 * })
 */
class Product implements EventBus
{
    use EventBusTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
     */
    private $enable = true;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     */
    private $popular = 0;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="product", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id", unique=false)
     */
    private $categories;

    /**
     * @ORM\Embedded(class="Information", columnPrefix="info_")
     */
    private $information;

    /**
     * @ORM\Embedded(class="Price", columnPrefix="price_")
     */
    private $price;

    /**
     * @ORM\Embedded(class="Seo", columnPrefix="seo_")
     */
    private $seo;

    /**
     * @ORM\OneToMany(targetEntity="Composition", mappedBy="product", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $composition;

    /**
     * @ORM\Column(type="ecommerce.product.attributes", nullable=true, options={"jsonb":true})
     */
    private $attributes;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="product", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="Recommended", mappedBy="product", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $recommended;

    /**
     * @ORM\OneToMany(targetEntity="App\Model\Review\Entity\Review", mappedBy="product", orphanRemoval=true, cascade={"persist","remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $reviews;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(
        bool $state,
        Information $info,
        Price $price,
        Seo $seo,
        ArrayCollection $attributes,
        int $popular
    )
    {
        $this->information = $info;
        $this->price = $price;
        $this->seo = $seo;
        $this->attributes = $attributes;
        $this->enable = $state;
        $this->popular = $popular;

        $this->composition = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->recommended = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function disable(): self
    {
        $this->enable = false;
        return $this;
    }

    public function enable(): self
    {
        $this->enable = true;
        return $this;
    }

    public function getPopular(): int
    {
        return $this->popular;
    }

    public function updatePopular(int $popular): self
    {
        $this->popular = $popular;
        return $this;
    }

    public function getEnableStatus(): bool
    {
        return $this->enable;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getFinishPrice(): float
    {
        if ($this->price->isFinalPrice()) {
            return $this->price->getCurrent();
        }

        $finalPrice = $this->price->getCurrent();
        foreach ($this->composition as $element) {
            $finalPrice += $element->getElement()->getPrice()->getCurrent() * $element->getCount();
        }
        return $finalPrice;
    }

    public function getFinishWeight(): float
    {
        if ($this->getInfo()->isFinalWeight()) {
            return $this->getInfo()->getWeight();
        }

        $final = $this->getInfo()->getWeight();
        foreach ($this->composition as $element) {
            $final += $element->getElement()->getInfo()->getWeight() * $element->getCount();
        }
        return $final;
    }

    public function getFinishVolume(): float
    {
        if ($this->getInfo()->isFinalVolume()) {
            return $this->getInfo()->getVolume();
        }

        $final = $this->getInfo()->getVolume();
        foreach ($this->composition as $element) {
            $final += $element->getElement()->getInfo()->getVolume() * $element->getCount();
        }
        return $final;
    }

    public function changePrice(Price $price): self
    {
        $this->price = $price;
        return $this;
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

    public function getInfo(): Information
    {
        return $this->information;
    }

    public function updateInfo(Information $information): self
    {
        $this->information = $information;
        return $this;
    }

    public function updateAttributes(ArrayCollection $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function addRecommended(Recommended $product): self
    {
        if ($this->recommended->contains($product)) {
            return $this;
        }
        $this->recommended->add($product);
        return $this;
    }

    public function getRecommended(): array
    {
        return $this->recommended->toArray();
    }

    public function removeRecommended(Recommended $product): self
    {
        $this->recommended->removeElement($product);
        return $this;
    }

    public function addInComposition(Composition $product): self
    {
        if ($this->composition->contains($product)) {
            return $this;
        }
        $this->composition->add($product);
        return $this;
    }

    public function getComposition(): array
    {
        return $this->composition->toArray();
    }

    public function removeFromComposition(Composition $product): self
    {
        $this->composition->removeElement($product);
        return $this;
    }

    public function getAttributes(): ArrayCollection
    {
        return $this->attributes;
    }

    public function addImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            return $this;
        }
        $this->images->add($image);
        return $this;
    }

    public function getImages(): array
    {
        return $this->images->toArray();
    }

    public function getCover(): ?File
    {
        foreach ($this->images as $img) {
            if ($img->isCover()) {
                return $img->getFile();
            }
        }
        return null;
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);
        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories->toArray();
    }

    public function getMainCategory(): ?\App\Model\Ecommerce\Entity\Category\Category
    {
        foreach ($this->categories as $category) {
            if ($category->isMain()) {
                return $category->getCategory();
            }
        }
        return null;
    }

    public function removeCategories(Category $category): self
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function addCategories(Category $category): self
    {
        if ($this->categories->contains($category)) {
            return $this;
        }
        $this->categories->add($category);
        return $this;
    }

    public function getReviews(): array
    {
        return $this->reviews->toArray();
    }

    public function getApproveReviews(): array
    {
        return array_filter($this->reviews->toArray(), static function (Review $review) {
            return $review->getStatus()->isActive();
        });
    }

    public function getAvgRating(): int
    {
        return (int)round(array_reduce($approve = $this->getApproveReviews(), static function (int $carry, Review $review) {
                return $carry + $review->getRating();
            }, 0) / (count($approve)>0?count($approve):1));
    }
}
