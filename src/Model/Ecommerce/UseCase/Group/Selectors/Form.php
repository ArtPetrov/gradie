<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Group\Selectors;

use App\Model\Ecommerce\Entity\Group;
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
            ->add('name', Type\HiddenType::class)
            ->add('type', Type\ChoiceType::class, [
                'choices' => [
                    'Выпадающий список' => Group\Type::SELECT,
                    'Радиокнопка' =>Group\Type::RADIO
                ],
            ])
            ->add('title', Type\TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
