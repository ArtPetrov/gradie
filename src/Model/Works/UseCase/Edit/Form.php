<?php

declare(strict_types=1);

namespace App\Model\Works\UseCase\Edit;

use App\Model\Works\Helper\DataTransformer\PositionTransformer;
use App\Model\Works\UseCase\Seo;
use App\Model\Works\UseCase\Content;
use App\Model\Works\UseCase\Image;
use App\Model\Works\UseCase\Composition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $positionTransformer;
    private $imageTransformer;

    public function __construct(PositionTransformer $positionTransformer, ImagesTransformer $imageTransformer)
    {
        $this->positionTransformer = $positionTransformer;
        $this->imageTransformer = $imageTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', Content\Form::class)
            ->add('images', Type\CollectionType::class,
                [
                    'entry_type' => Image\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('diy', Type\CollectionType::class,
                [
                    'entry_type' => Image\Diy\Form ::class,
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
            ->add('seo', Seo\Form::class);

        $builder->get('composition')->addModelTransformer($this->positionTransformer);
        $builder->get('images')->addModelTransformer($this->positionTransformer);
        $builder->get('images')->addModelTransformer($this->imageTransformer);
        $builder->get('diy')->addModelTransformer($this->positionTransformer);
        $builder->get('diy')->addModelTransformer($this->imageTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
