<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Edit;

use App\Model\Ecommerce\Helper\DataTransformer\PositionTransformer;
use App\Model\Ecommerce\Repository\CategoryRepository;
use App\Model\Ecommerce\UseCase\Category\Seo;
use App\Model\Ecommerce\UseCase\Category\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @var CategoryRepository
     */
    private $categories;
    /**
     * @var PositionTransformer
     */
    private $positionTransformer;

    public function __construct(CategoryRepository $categories, PositionTransformer $positionTransformer)
    {
        $this->categories = $categories;
        $this->positionTransformer = $positionTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('type', Type\ChoiceType::class, ['choices' => ['Купить'=>'BUY','Подробнее'=>'VIEW']])
            ->add('parent', Type\ChoiceType::class, [
                'choices' => $this->categories->getCategoriesForSelect("----")
            ])
            ->add('seo', Seo\Form::class)
            ->add('filters', Type\CollectionType::class,
                [
                    'entry_type' => Filter\Form::class,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );


        $builder->get('filters')->addModelTransformer($this->positionTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Command::class,
        ]);
    }
}