<?php

declare(strict_types=1);

namespace App\Model\Dealer\UseCase\Edit\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('site', Type\TextType::class, ['required' => false])
            ->add('phone', Type\TelType::class)
            ->add('contrahens', Type\TelType::class, ['required' => false])
            ->add('notification', Type\CheckboxType::class, ['required' => false])
            ->add('inn', Type\TextType::class, ['required' => false])
            ->add('kpp', Type\TextType::class, ['required' => false])
            ->add('address', Type\TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}