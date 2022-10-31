<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute\Rename;

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
        $queryRename = <<<EOF
UPDATE ecommerce_product product
SET attributes =
    (SELECT (prod.attributes - :slug || jsonb_build_object( :slug_new, prod.attributes->:slug ))
     FROM ecommerce_product prod
     WHERE product.id = prod.id)::jsonb
WHERE jsonb_exists(product.attributes, :slug ) AND :slug_new <> :slug 
EOF;

        $statement = $this->connection->prepare($queryRename);
        $statement->bindValue('slug_new', $command->newSlug);
        $statement->bindValue('slug', $command->currentSlug);
        $statement->execute();
    }

}