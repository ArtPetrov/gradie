<?php

declare(strict_types=1);

namespace App\Model\DesignProject\UseCase\Create\Type1;

use App\Model\DesignProject\UseCase\Client;
use App\Model\DesignProject\UseCase\Project;
use App\Model\DesignProject\UseCase\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $sizeTransformer;

    public function __construct(SizeTransformer $sizeTransformer)
    {
        $this->sizeTransformer = $sizeTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', Client\Form::class)
            ->add('project', Project\Form::class)
            ->add('size', Size\Form::class)
            ->add('files', Type\CollectionType::class,
                [
                    'entry_type' => File\Form ::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );

        $builder->get('size')->addModelTransformer($this->sizeTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
