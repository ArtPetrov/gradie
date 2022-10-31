<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\Address\Cities\Moscow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', Type\TextType::class, ['required' => false])
            ->add('address', Type\TextType::class, ['required' => false])
            ->add('howManyKm', Type\IntegerType::class, [
                'required' => false
            ])
            ->add('freeShippingLimit', Type\HiddenType::class)
            ->add('baseCostShipping', Type\HiddenType::class)
            ->add('costKmShipping', Type\HiddenType::class)
            ->add('deliveryTo', Type\ChoiceType::class, [
                'choices' => [
                    'В пределах МКАД. <strong>500₽</strong>. <span class="ordering__light">Бесплатно при заказе от 30000₽</span>' => 'mkad',
                    'За МКАД. <strong>500₽ + 30₽/км</strong>' => 'abroad'
                ],
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
