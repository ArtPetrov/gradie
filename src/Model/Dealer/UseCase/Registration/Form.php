<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Registration;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('frod_a', Type\HiddenType::class)
            ->add('frod_b', Type\HiddenType::class)
            ->add('frod_result', Type\HiddenType::class)
            ->add('site', Type\TextType::class)
            ->add('company', Type\TextType::class)
            ->add('city', Type\TextType::class)
            ->add('name', Type\TextType::class)
            ->add('city', Type\TextType::class)
            ->add('name', Type\TextType::class)
            ->add('phone', Type\TelType::class)
            ->add('leader', Type\TextType::class, ['required' => false])
            ->add('email', Type\EmailType::class)
            ->add('profile', Type\TextType::class)
            ->add('site', Type\TextType::class, ['required' => false])
            ->add('why_we', Type\TextareaType::class, ['required' => false])
            ->add('how_know', Type\TextareaType::class, ['required' => false])
            ->add('experience', Type\TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}