<?php

declare(strict_types=1);

namespace App\Model\File\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="file_temporary",
 * indexes={
 *     @ORM\Index(name="file_id_idx", columns={"file_id"})
 * })
 */
class FileTemporary
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="File", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFile(): File
    {
        return $this->file;
    }

}
