<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use App\Model\Mailer\Entity\Mailer;
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
            ->add('type', Type\ChoiceType::class,['choices'  => ['Рассылка по подписке' => Mailer::TYPE_MAILING, 'Письмо' => Mailer::TYPE_MAIL]])
            ->add('recipient',RecipientType::class)
            ->add('sender',SenderType::class)
            ->add('mail',MailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}