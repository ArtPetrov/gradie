<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class MailType extends AbstractType
{

    /**
     * @var FilesTransformer
     */
    private $filesTransformer;

    public function __construct(FilesTransformer $filesTransformer)
    {
        $this->filesTransformer = $filesTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('header', Type\TextType::class)
            ->add('content', Type\TextareaType::class, ['required' => false])
            ->add('files', Type\HiddenType::class, ['required' => false])
            ->get('files')->addModelTransformer($this->filesTransformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Mail::class,
        ));
    }
}