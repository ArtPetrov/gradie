<?php

declare(strict_types=1);

namespace App\Helper;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GeneratorSlug extends AbstractController
{
    public function make(string $value): ?string
    {
        $urlizer = new Urlizer();
        return $urlizer->transliterate($value);
    }
}
