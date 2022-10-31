<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Information;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', Type\TextType::class)
            ->add('name', Type\TextType::class)
            ->add('timetable', Type\TextType::class, ['required' => false])
            ->add('phone', Type\TextType::class, ['required' => false])
            ->add('email', Type\TextType::class, ['required' => false])
            ->add('site', Type\TextType::class, ['required' => false])
            ->add('comment', Type\TextType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}

