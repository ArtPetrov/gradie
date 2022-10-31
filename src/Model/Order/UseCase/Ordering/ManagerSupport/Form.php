<?php

declare(strict_types=1);

namespace App\Model\Order\UseCase\Ordering\ManagerSupport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('help', Type\ChoiceType::class, [
                'choices' => [
                    'Мне нужна помощь менеджера' => 'need',
                    'Помощь менеджера не нужна' => 'cancel'
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
