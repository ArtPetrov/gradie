<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Create;

use Symfony\Component\Form\AbstractType;
use App\Model\Promocode\Entity;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', Type\ChoiceType::class, ['choices' => [
                'Скидка суммой' => Entity\Type::MONEY,
                'Скидка в процентах' => Entity\Type::PROCENT]
            ])
            ->add('enable', Type\CheckboxType::class, ['required' => false])
            ->add('value', Type\NumberType::class)
            ->add('information', InformationType::class)
            ->add('restrictions', RestrictionsType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
