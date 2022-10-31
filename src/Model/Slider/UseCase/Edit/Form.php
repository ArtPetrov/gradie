<?php

declare(strict_types=1);

namespace App\Model\Slider\UseCase\Edit;

use App\Model\Slider\UseCase\Button;
use App\Model\Slider\UseCase\Info;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enable', Type\CheckboxType::class, ['required' => false])
            ->add('info', Info\Form::class)
            ->add('button', Button\Form::class)
            ->add('cover', Type\FileType::class, [
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '6M',
                        'mimeTypes' => ['image/*'],
                        'mimeTypesMessage' => 'cover.not.valid',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
