<?php

namespace App\Model\Buyer\UseCase\SignUp\Request;

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
            ->add('phone', Type\TelType::class)
            ->add('email', Type\EmailType::class)
            ->add('password', Type\PasswordType::class)
            ->add('repeatPassword', Type\PasswordType::class)
            ->add('a', Type\HiddenType::class)
            ->add('b', Type\HiddenType::class)
            ->add('c', Type\HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
