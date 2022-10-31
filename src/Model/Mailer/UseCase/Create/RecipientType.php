<?php

declare(strict_types=1);

namespace App\Model\Mailer\UseCase\Create;

use App\Model\Cpanel\Entity\CategoryDealer;
use App\Model\Cpanel\Repository\CategoryDealerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipientType extends AbstractType
{
    /**
     * @var CategoryDealerRepository
     */
    private $categories;

    public function __construct(CategoryDealerRepository $categories)
    {
        $this->categories = $categories;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', Type\ChoiceType::class, ['choices' => [
                'Всем' => Recipient::TYPE_ALL,
                'По категориям' => Recipient::TYPE_CATEGORY,
                'По Email' => Recipient::TYPE_EMAIL
            ]])
            ->add('emails', Type\TextType::class, ['required' => false])
            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => CategoryDealer::class,
                'query_builder' => function (CategoryDealerRepository $repository) {
                    return $repository->createQueryBuilder('category')
                        ->orderBy('category.position', 'DESC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Recipient::class,
        ));
    }
}