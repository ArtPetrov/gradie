<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer\Edit;

use App\Model\Salon\UseCase\Information;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('info', Information\Form::class)
            ->add('moderation', Information\Form::class)
            ->add('moderationComment', Type\TextareaType::class, ['required' => false])
            ->add('comment', Type\TextareaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}

