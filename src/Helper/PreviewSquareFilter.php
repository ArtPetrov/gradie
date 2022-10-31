<?php

declare(strict_types=1);

namespace App\Helper;

use Imagine\Image\ImageInterface;
use Imagine\Filter\Basic\Thumbnail;
use Imagine\Image\Box;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class PreviewSquareFilter implements LoaderInterface
{
    private $filterManager;

    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function load(ImageInterface $image, array $options = array())
    {
        $minSize = min($image->getSize()->getWidth(), $image->getSize()->getHeight());
        $filter = new Thumbnail(new Box($minSize, $minSize), ImageInterface::THUMBNAIL_OUTBOUND);
        $image = $filter->apply($image);
        return $image;
    }
}