<?php

declare(strict_types=1);

namespace App\Twig\Frontend;

use App\Model\Ecommerce\Entity\Attribute\Field;
use App\Model\Ecommerce\ReadModel\Attribute\AttributeFetcher;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductAttributeWidget extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('product_attribute', [$this, 'getAttribute'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function getAttribute(Environment $twig, object $attr): string
    {
        if (Field::BOOL == $attr->type) {
            return $this->render($twig, (string)$attr->label, $attr->value ? 'Да' : 'Нет');
        }

        if (Field::CHECKBOX !== $attr->type && Field::SELECT !== $attr->type) {
            return $this->render($twig, (string)$attr->label, (string)$attr->value);
        }

        $fetcher = $this->container->get(AttributeFetcher::class);
        $attribute = $fetcher->getBySlug($attr->slug, $attr->value);
        return $this->render($twig, (string)$attr->label, $attribute->getLabelForValue());

    }

    public function render(Environment $twig, string $label, ?string $value): string
    {
        return $twig->render('widget/frontend/product_attribute.html.twig', [
            'value' => $value,
            'label' => $label
        ]);
    }

}
