<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Information;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enable', Type\CheckboxType::class)
            ->add('article', Type\TextType::class)
            ->add('name', Type\TextType::class)
            ->add('content', Type\TextareaType::class)
            ->add('youtube', Type\TextType::class)
            ->add('price', Type\NumberType::class, ['scale' => 2])
            ->add('priceFinal', Type\CheckboxType::class)
            ->add('priceOld', Type\NumberType::class, ['scale' => 2])
            ->add('weight', Type\NumberType::class, ['scale' => 3])
            ->add('weightIsFinal', Type\CheckboxType::class)
            ->add('volume', Type\NumberType::class, ['scale' => 3])
            ->add('volumeIsFinal', Type\CheckboxType::class)
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}