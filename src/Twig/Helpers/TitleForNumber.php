<?php

declare(strict_types=1);

namespace App\Twig\Helpers;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TitleForNumber extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('title_for_count', [$this, 'makeTitle'], ['needs_environment' => false, 'is_safe' => ['html']])
        ];
    }

    public function makeTitle(int $count, array $words = []): string
    {
        $keys = [2, 0, 1, 1, 1, 2];
        $mod = $count % 100;
        $index = $mod > 4 && $mod < 20 ? 2 : $keys[min($mod%10, 5)];
        return $words[$index];
    }
}
