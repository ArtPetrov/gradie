<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quiz\Edit;

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
            ->add('name', Type\TextType::class)
            ->add('enable', Type\CheckboxType::class, ['required' => false])
            ->add('textBegin', Type\TextType::class)
            ->add('textEnd', Type\TextType::class)
            ->add('content', Type\TextareaType::class, ['required' => false])
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
