<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Mail
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="6")
     */
    public $header;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="16")
     */
    public $content;

    public $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }
}
