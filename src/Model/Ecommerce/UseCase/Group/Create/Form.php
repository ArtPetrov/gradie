<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Create;

use App\Model\Ecommerce\Helper\DataTransformer\PositionTransformer;
use App\Model\Ecommerce\UseCase\Group\Product;
use App\Model\Ecommerce\UseCase\Group\Selectors;
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
            ->add('name', Type\TextType::class)
            ->add('products', Type\CollectionType::class,
                [
                    'entry_type' => Product\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add('selectors', Type\CollectionType::class,
                [
                    'entry_type' => Selectors\Form::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );

        $builder->get('selectors')->addModelTransformer($this->positionTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
