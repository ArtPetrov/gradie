<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Edit;

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
            $value[$key]->src = $this->filterImages->getUrlOfFilteredImage($element->src, 'work_148_105');
        }
        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}
