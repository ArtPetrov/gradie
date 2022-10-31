<?php

declare(strict_types=1);

namespace App\Twig\Mailer;

use App\Model\Mailer\Repository\RecipientRepository;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatisticsWidget extends AbstractExtension
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('mailer_statistics', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function status(Environment $twig, int $id): string
    {
        $repository = $this->container->get(RecipientRepository::class);

        return $twig->render('widget/mailer/statistics.html.twig', [
            'sent' => $repository->completedForMailer($id),
            'total' =>  $repository->totalForMailer($id)
        ]);
    }
}
