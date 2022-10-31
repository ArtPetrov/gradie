<?php

declare(strict_types=1);

namespace App\Model\Promocode\UseCase\Edit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class RestrictionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStart', Type\DateTimeType::class, [
                'input' => 'datetime_immutable',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'input_format' => 'd.m.Y H:i',
                'required' => false
            ])
            ->add('dateEnd', Type\DateTimeType::class, [
                'input' => 'datetime_immutable',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'input_format' => 'd.m.Y H:i',
                'required' => false
            ])
            ->add('minSumOrder', Type\NumberType::class)
            ->add('maxSumOrder', Type\NumberType::class)
            ->add('countLimit', Type\IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Restrictions::class,
        ));
    }
}
