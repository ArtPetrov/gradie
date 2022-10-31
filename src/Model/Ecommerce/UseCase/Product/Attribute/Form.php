<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', Type\HiddenType::class)
            ->add('type', Type\HiddenType::class)
            ->add('value', Type\HiddenType::class)
            ->add('label', Type\TextType::class)
            ->add('visible', Type\CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}