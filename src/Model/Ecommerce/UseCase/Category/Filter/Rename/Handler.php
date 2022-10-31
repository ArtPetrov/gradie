<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Category\Filter\Rename;

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
UPDATE ecommerce_category category
SET filters =
    (SELECT (cat.filters - :slug || jsonb_build_object( :slug_new, cat.filters->:slug ))
     FROM ecommerce_category cat
     WHERE category.id = cat.id)::jsonb
WHERE jsonb_exists(category.filters, :slug ) AND :slug_new <> :slug 
EOF;

        $statement = $this->connection->prepare($queryRename);
        $statement->bindValue('slug_new', $command->newSlug);
        $statement->bindValue('slug', $command->currentSlug);
        $statement->execute();
    }

}