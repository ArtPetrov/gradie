<?php

declare(strict_types=1);

namespace App\Model\Gallery\Entity;

use App\Model\File\Entity\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_gallery_images",
 * indexes={
 *     @ORM\Index(name="position_idx_photo", columns={"position"}),
 *     @ORM\Index(name="album_idx_photo", columns={"album_id"}),
 *     @ORM\Index(name="album_cover_idx_photo", columns={"album_id", "cover"}, options={"where": "cover"})
 * })
 */
class Image
{
    public const DIRECTORY_FILES = 'gallery';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="images")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id",nullable=false)
     */
    private $album;

    /**
     * @ORM\OneToOne(targetEntity="App\Model\File\Entity\File", cascade = {"persist","remove"}, orphanRemoval=true, fetch="EAGER")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
     */
    private $file;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private $cover;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default":0,"unsigned":true})
     */
    private $position;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(Album $album, File $file, bool $cover, int $position = 0)
    {
        $this->album = $album;
        $this->file = $file;
        $this->cover = $cover;
        $this->position = $position;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlbum(): Album
    {
        return $this->album;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function isCover(): bool
    {
        return $this->cover;
    }

    public function setCover(bool $cover): self
    {
        $this->cover = $cover;
        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
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
}