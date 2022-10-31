<?php

declare(strict_types=1);

namespace App\Model\Article\UseCase\Create;

use App\Model\Article\Helper\DataTransformer\PositionTransformer;
use App\Model\Article\UseCase\Seo;
use App\Model\Article\UseCase\Image;
use App\Model\Article\UseCase\Name;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $positionTransformer;

    public function __construct(PositionTransformer $positionTransformer)
    {
        $this->positionTransformer = $positionTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Name\Form::class)
            ->add('images', Type\CollectionType::class,
                [
                    'entry_type' => Image\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('seo', Seo\Form::class)
            ->add('publishedAt', Type\DateTimeType::class, [
                'input' => 'datetime_immutable',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'input_format' => 'd.m.Y H:i'
            ])
            ->add('content', Type\TextareaType::class, ['required' => false]);

        $builder->get('images')->addModelTransformer($this->positionTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
