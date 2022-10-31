<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute\Remove;

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
        $stmt = $this->connection->prepare("UPDATE ecommerce_product product SET attributes = product.attributes - :slug  WHERE jsonb_exists(product.attributes, :slug )");
        $stmt->bindValue(":slug", $command->slug, ParameterType::STRING);
        $stmt->execute();
    }
}
