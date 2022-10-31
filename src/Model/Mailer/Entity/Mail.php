<?php

declare(strict_types=1);

namespace App\Model\Mailer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Mailer\Repository\MailRepository")
 * @ORM\Table(name="mailer_mail")
 */
class Mail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=126, nullable=false)
     */
    private $header;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="File", mappedBy="mail", orphanRemoval=true, fetch="EAGER", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $files;

    public function __construct(string $header, string $content)
    {
        $this->header = $header;
        $this->content = $content;
        $this->files = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function getMailer(): ?Mailer
    {
        return $this->mailer;
    }

    public function changeHeader(?string $header): self
    {
        $this->header = $header;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function attachFile(File $file): self
    {
        if ($this->files->contains($file)) {
            return $this;
        }
        $this->files[] = $file;
        $file->setMail($this);
        return $this;
    }

    public function removeFile(File $file): self
    {
        $this->files->removeElement($file);
        $file->setMail(null);
        return $this;
    }

    public function getFiles(): ?Collection
    {
        return $this->files;
    }
}
