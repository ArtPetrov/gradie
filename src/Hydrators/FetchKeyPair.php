<?php

declare(strict_types=1);

namespace App\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class FetchKeyPair extends AbstractHydrator
{
    protected function hydrateAllData(): array
    {
        return $this->_stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

    }
}