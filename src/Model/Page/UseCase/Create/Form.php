<?php

declare(strict_types=1);

namespace App\Model\Page\UseCase\Create;

use App\Model\Page\Transformer\PageTransformer;
use App\Model\Page\UseCase\Seo;
use App\Model\Page\UseCase\Settings;
use App\Model\Page\UseCase\Content;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $pageTransformer;

    public function __construct(PageTransformer $pageTransformer)
    {
        $this->pageTransformer = $pageTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('content', Content\Form::class)
            ->add('settings', Settings\Form::class)
            ->add('seo', Seo\Form::class);

        $builder->addModelTransformer($this->pageTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}
