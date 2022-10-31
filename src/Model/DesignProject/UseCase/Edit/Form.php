<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Edit;

use App\Model\DesignProject\Entity\Status;
use App\Model\DesignProject\UseCase\Client;
use App\Model\DesignProject\UseCase\Project;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', Type\TextareaType::class, ['required' => false])
            ->add('status', Type\ChoiceType::class, ['choices' => Status::list()])
            ->add('client', Client\Form::class)
            ->add('project', Project\Form::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
