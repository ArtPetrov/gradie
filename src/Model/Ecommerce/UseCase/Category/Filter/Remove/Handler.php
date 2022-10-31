<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Filter\Remove;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $connection;

    public function __construct(EntityManagerInterface $em)
    {
        $this->connection = $em->getConnection();
    }

    public function handle(Command $command): void
    {
        $stmt = $this->connection->prepare("UPDATE ecommerce_category category SET filters = category.filters - :slug  WHERE jsonb_exists(category.filters, :slug )");
        $stmt->bindValue(":slug", $command->slug, ParameterType::STRING);
        $stmt->execute();
    }
}
