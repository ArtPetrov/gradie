<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type1\Size;

use App\Model\DesignProject\UseCase\Client;
use App\Model\DesignProject\UseCase\Project;
use App\Model\DesignProject\UseCase\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('a', Type\TextType::class)
            ->add('b', Type\TextType::class)
            ->add('c', Type\TextType::class)
            ->add('d', Type\TextType::class)
            ->add('e', Type\TextType::class)
            ->add('hp', Type\TextType::class)
            ->add('hd', Type\TextType::class)
            ->add('wd', Type\TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
