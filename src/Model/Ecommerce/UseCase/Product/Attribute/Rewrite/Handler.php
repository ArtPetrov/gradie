<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\UseCase\Product\Attribute\Rewrite;

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
        $oldSlug = $command->oldValues->toArray();
        $newSlug = $command->newValues->toArray();

        $removeValues = [];
        $renameValues = [];
        foreach ($oldSlug as $value) {
            foreach ($newSlug as $new) {
                if ($new->value === $value->value) {
                    continue 2;
                }
                if ($new->label === $value->label) {
                    $renameValues[] = ['old' => $value->value, 'new' => $new->value];
                    continue 2;
                }
            }
            $removeValues[] = $value;
        }

        $this->removeFields($command->slug, $removeValues);

        $this->renameFields($command->slug, $renameValues);
    }

    private function removeFields(string $slug, array $values): void
    {
        foreach ($values as $val) {

            $query = '{"' . $slug . '":{"value":"' . $val->value . '"}}';

            $queryRemove = <<<EOF
UPDATE ecommerce_product product
SET attributes = product.attributes - :slug    
WHERE product.attributes @> :query::jsonb
EOF;
            $statement = $this->connection->prepare($queryRemove);
            $statement->bindValue("query", $query);
            $statement->bindValue("slug", $slug);
            $statement->execute();
        }

    }

    private function renameFields(string $slug, array $values): void
    {
        foreach ($values as $val) {
            $queryUpdate = <<<EOF
UPDATE ecommerce_product product
SET attributes = jsonb_set(product.attributes, '{{$slug},value}','"{$val['new']}"', false) 
WHERE product.attributes @> '{"{$slug}":{"value":"{$val['old']}"}}'::jsonb  
EOF;
            $statement = $this->connection->prepare($queryUpdate);
            $statement->execute();
        }
    }

}