<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Edit\Administrator;

use App\Model\Review\Entity\Status;
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
            ->add('message', Type\TextareaType::class)
            ->add('rating', Type\ChoiceType::class, ['choices' => [
                '5 звезд' => 5,
                '4 звезды' => 4,
                '3 звезды' => 3,
                '2 звезды' => 2,
                '1 звезда' => 1,
            ]])
            ->add('status', Type\ChoiceType::class, ['choices' => [
                'Активный' => Status::STATUS_ACTIVE,
                'На модерации' => Status::STATUS_WAIT,
                'Заблокирован' => Status::STATUS_BLOCKED,
            ]])
            ->add('createAt', Type\DateTimeType::class, [
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'input_format' => 'd.m.Y H:i'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
