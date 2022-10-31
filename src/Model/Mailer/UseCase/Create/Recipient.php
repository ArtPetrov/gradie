<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Recipient
{
    public const TYPE_ALL = 'all';
    public const TYPE_CATEGORY = 'category';
    public const TYPE_EMAIL = 'email';

    /**
     * @Assert\NotBlank()
     */
    public $type;

    public $emails;
    public $categories;

    public function __construct(?string $email = null)
    {
        $this->type = self::TYPE_ALL;

        if($email){
            $this->type = self::TYPE_EMAIL;
            $this->emails = $email;
        }
    }


}
