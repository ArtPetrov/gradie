<?php

declare(strict_types=1);

namespace App\Model\Salon\UseCase\Dealer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $dealerTransformer;

    public function __construct(DealerTransformer $dealerTransformer)
    {
        $this->dealerTransformer = $dealerTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', Type\HiddenType::class, ['required' => false])
            ->add('name', Type\TextType::class, ['required' => false])
        ;
        $builder->addModelTransformer($this->dealerTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}

