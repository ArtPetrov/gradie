<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Attribute\Edit;

use App\Model\Ecommerce\Entity\Attribute\Field;
use App\Model\Ecommerce\UseCase\Attribute\FieldValue;
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
            ->add('type', Type\ChoiceType::class, [
                'choices' => Field::allTypesFields(),
                'placeholder' => 'Выбрать',
                'disabled' => 'true',
            ])
            ->add('values', Type\CollectionType::class,
                [
                    'entry_type' => FieldValue\Form::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}