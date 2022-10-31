<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', Type\ChoiceType::class, [
                'choices' => [
                    'Москва и Московская область' => 'moscow',
                    'Регионы' => 'regions',
                    'Самовывоз' => 'pickup',
                ],
            ])
            ->add('orderPrice', Type\HiddenType::class)
            ->add('orderPriceWithPromo', Type\HiddenType::class)
            ->add('moscow', Cities\Moscow\Form::class)
            ->add('regions', Cities\Regions\Form::class)
            ->add('pickup', Cities\Pickup\Form::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
