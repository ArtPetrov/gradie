<?php

declare(strict_types=1);

namespace App\Model\Page\Transformer;

use App\Helper\GeneratorSlug;
use Symfony\Component\Form\DataTransformerInterface;

class PageTransformer implements DataTransformerInterface
{
    private $generatorSlug;

    public function __construct(GeneratorSlug $generatorSlug)
    {
        $this->generatorSlug = $generatorSlug;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        if (!$value->content->header) {
            return $value;
        }

        if (!$value->name) {
            $value->name = $value->content->header;
        }

        $value->settings->status = (int)$value->settings->status;

        if (!$value->settings->slug) {
            $value->settings->slug = $this->generatorSlug->make($value->content->header);
        }

        return $value;
    }
}
