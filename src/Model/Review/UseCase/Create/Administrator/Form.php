<?php

declare(strict_types=1);

namespace App\Model\Review\UseCase\Create\Administrator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Form extends AbstractType
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAction($this->urlGenerator->generate('cpanel.review.create'))
            ->add('product', Type\HiddenType::class)
            ->add('name', Type\TextType::class)
            ->add('message', Type\TextareaType::class)
            ->add('rating', Type\ChoiceType::class, ['choices' => [
                '5 звезд' => 5,
                '4 звезды' => 4,
                '3 звезды' => 3,
                '2 звезды' => 2,
                '1 звезда' => 1,
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
