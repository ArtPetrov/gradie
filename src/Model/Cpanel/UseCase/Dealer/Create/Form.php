<?php

declare(strict_types=1);

namespace App\Model\Cpanel\UseCase\Dealer\Create;

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
            ->add('name', Type\TextType::class)
            ->add('email', Type\EmailType::class)
            ->add('site', Type\TextType::class, ['required' => false])
            ->add('phone', Type\TelType::class)
            ->add('notification', Type\CheckboxType::class, ['required' => false])
            ->add('inn', Type\TextType::class, ['required' => false])
            ->add('kpp', Type\TextType::class, ['required' => false])
            ->add('contrahens', Type\TextType::class, ['required' => false])
            ->add('address', Type\TextType::class, ['required' => false])
            ->add('manager', Type\ChoiceType::class, [
                'choices' => $this->managers->list(),
                'required' => false,
                'placeholder' => 'Выбрать менеджера',
            ])
            ->add('category', Type\ChoiceType::class, [
                'choices' => $this->categories->list(),
                'required' => false,
                'placeholder' => 'Выбрать категорию',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}