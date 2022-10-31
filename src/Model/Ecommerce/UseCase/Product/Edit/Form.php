<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Edit;

use App\Model\Ecommerce\Helper\DataTransformer\PositionTransformer;
use App\Model\Ecommerce\UseCase\Product\Information;
use App\Model\Ecommerce\UseCase\Product\Seo;
use App\Model\Ecommerce\UseCase\Product\Recommended;
use App\Model\Ecommerce\UseCase\Product\Composition;
use App\Model\Ecommerce\UseCase\Product\Image;
use App\Model\Ecommerce\UseCase\Product\Category;
use App\Model\Ecommerce\UseCase\Product\Attribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $positionTransformer;
    private $imageTransformer;
    private $categoryTransformer;

    public function __construct(PositionTransformer $positionTransformer, ImagesTransformer $imageTransformer, Category\CategoryTransformer $categoryTransformer)
    {
        $this->positionTransformer = $positionTransformer;
        $this->imageTransformer = $imageTransformer;
        $this->categoryTransformer = $categoryTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('popular', Type\NumberType::class)
            ->add('information', Information\Form::class)
            ->add('seo', Seo\Form::class)
            ->add('categories', Category\Form::class)
            ->add('images', Type\CollectionType::class,
                [
                    'entry_type' => Image\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('composition', Type\CollectionType::class,
                [
                    'entry_type' => Composition\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('recommended', Type\CollectionType::class,
                [
                    'entry_type' => Recommended\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('attributes', Type\CollectionType::class,
                [
                    'entry_type' => Attribute\Form::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );

        $builder->get('recommended')->addModelTransformer($this->positionTransformer);
        $builder->get('composition')->addModelTransformer($this->positionTransformer);
        $builder->get('attributes')->addModelTransformer($this->positionTransformer);
        $builder->get('images')->addModelTransformer($this->positionTransformer);
        $builder->get('images')->addModelTransformer($this->imageTransformer);
        $builder->get('categories')->addModelTransformer($this->categoryTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}