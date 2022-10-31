<?php

declare(strict_types=1);

namespace App\Model\Gallery\UseCase\Edit;

use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\Form\DataTransformerInterface;

class ImagesTransformer implements DataTransformerInterface
{
    private $filterImages;

    public function __construct(FilterService $filterImages)
    {
        $this->filterImages = $filterImages;
    }

    public function transform($value)
    {
        foreach ($value as $key => $element) {
            $value[$key]->src = $this->filterImages->getUrlOfFilteredImage($element->src, 'gallery_215_215');
        }
        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}
