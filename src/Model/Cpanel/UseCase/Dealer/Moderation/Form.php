<?php

namespace App\Model\Cpanel\UseCase\Dealer\Moderation;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\Cpanel\Repository\ManagerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @var CategoryDealerRepository
     */
    private $categories;
    /**
     * @var ManagerRepository
     */
    private $managers;

    public function __construct(CategoryDealerRepository $categories, ManagerRepository $managers)
    {
        $this->categories = $categories;
        $this->managers = $managers;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', Type\ChoiceType::class, [
                'choices' => $this->categories->list(),
                'required' => false,
                'placeholder' => 'Укажите категорию',
            ])
            ->add('managers', Type\ChoiceType::class, [
                'choices' => $this->managers->list(),
                'required' => false,
                'placeholder' => 'Назначьте менеджера',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

}
