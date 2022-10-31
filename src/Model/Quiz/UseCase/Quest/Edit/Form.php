<?php

declare(strict_types=1);

namespace App\Model\Quiz\UseCase\Quest\Edit;

use App\Model\Quiz\Entity\QuestType;
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
            ->add('quest', Type\TextType::class)
            ->add('help', Type\TextType::class, ['required' => false])
            ->add('anotherAnswer', Type\CheckboxType::class, ['required' => false])
            ->add('skip', Type\CheckboxType::class, ['required' => false])
            ->add('type', Type\ChoiceType::class, [
                'disabled' => true,
                'choices' => array_flip(QuestType::getTypes())
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
