<?php

namespace App\Model\Cpanel\UseCase\Filter\Dealers;

use App\Model\Cpanel\Repository\CategoryDealerRepository;
use App\Model\Cpanel\Repository\ManagerRepository;
use App\Model\Dealer\Entity\Status;
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
            ->add('query', Type\SearchType::class, [
                'required' => false,
                'attr' => ['onchange' => 'this.form.submit()']
            ])
            ->add('status', Type\ChoiceType::class, [
                'choices' => ['В ожидании' => Status::STATUS_WAIT, 'Активные' => Status::STATUS_ACTIVE, 'Заблокированые' => Status::STATUS_BLOCKED,],
                'required' => false,
                'placeholder' => 'Статус',
                'attr' => ['onchange' => 'this.form.submit()']
            ])
            ->add('category', Type\ChoiceType::class, [
                'choices' => $this->categories->list(),
                'required' => false,
                'placeholder' => 'Категория',
                'attr' => ['onchange' => 'this.form.submit()']
            ])
            ->add('manager', Type\ChoiceType::class, [
                'choices' => $this->managers->list(),
                'required' => false,
                'placeholder' => 'Менеджер',
                'attr' => ['onchange' => 'this.form.submit()']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
