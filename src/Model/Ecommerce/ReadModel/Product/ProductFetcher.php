<?php

declare(strict_types=1);

namespace App\Model\Ecommerce\ReadModel\Product;

use App\Model\Ecommerce\Entity\Attribute\Field;
use App\Model\Ecommerce\Entity\Product\Product;
use App\Model\Ecommerce\ReadModel\Attribute\AttributeForFilter;
use App\Model\Ecommerce\UseCase\Product\Search;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProductFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Product::class);
        $this->paginator = $paginator;
    }

    public function get(int $id): Product
    {
        /** @var Product $product */
        if (!$product = $this->repository->find($id)) {
            throw new \DomainException('product.not.found');
        }
        return $product;
    }

    public function findForRecommended(string $query): ?array
    {
        $qb = $this->findShortResult($query)->andWhere('product.enable = true');
        return $this->fetchAllShortResult($qb);
    }

    public function findForComposition(string $query): ?array
    {
        $qb = $this->findShortResult($query)->andWhere('product.price_final = true');
        return $this->fetchAllShortResult($qb);
    }

    public function findByArticleOrName(Search\Command $search, int $page, int $limit): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'product.id',
                'product.info_article as article',
                'product.info_name as name',
                'category.name as category_name'
            )
            ->from('ecommerce_product', 'product')
            ->leftJoin('product', 'ecommerce_product_category', 'category_link', 'category_link.product_id = product.id AND category_link.main=true')
            ->leftJoin('category_link', 'ecommerce_category', 'category', 'category_link.category_id = category.id')
            ->addSelect('category');

        if ($search->query) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(product.info_article, \' \', coalesce(product.info_name, \'\')))', ':query'));
            $qb->setParameter(':query', '%' . mb_strtolower($search->query) . '%');
        }

        $qb->orderBy("product.popular", 'ASC')->addOrderBy("product.info_article", 'ASC');

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function existsInComposition(int $id): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('id')
                ->from('ecommerce_product_composition')
                ->where('element_id = :id')
                ->setParameter(':id', $id, ParameterType::INTEGER)
                ->setMaxResults(1)
                ->execute()->fetchColumn() > 0;
    }

    public function existsInRecommended(int $id): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('id')
                ->from('ecommerce_product_recommended')
                ->where('recommened_id = :id')
                ->setParameter(':id', $id, ParameterType::INTEGER)
                ->setMaxResults(1)
                ->execute()->fetchColumn() > 0;
    }

    private function getVector(): string
    {
        return "(setweight(to_tsvector('russian', product.info_name), 'A') || setweight(to_tsvector('russian', coalesce('russian', product.info_content,'')), 'B'))";
    }

    private function findShortResult(string $query): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'product.id',
                'product.info_article as article',
                'product.info_name as name'
            )
            ->from('ecommerce_product', 'product');

        if ($query) {
            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(product.info_article, \' \', coalesce(product.info_name, \'\')))', ':query'));
            $qb->setParameter(':query', '%' . mb_strtolower($query) . '%');
        }
        return $qb;
    }

    private function fetchAllShortResult(QueryBuilder $qb): ?array
    {
        $qb->orderBy("product.info_article", 'ASC');
        $qb->setMaxResults(20);

        $stmt = $qb->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortProduct::class);
        return $stmt->fetchAll();
    }

    public function searchForQuery(string $query, int $limit = 12): array
    {
        $querySearch = <<<EOF
 WITH porducts_with_price AS(
    {$this->selectForWith()}
    FROM  ecommerce_product product
    WHERE 
    ((setweight(to_tsvector('russian', product.info_name), 'A') || setweight(to_tsvector('russian', coalesce('russian', product.info_content,'')), 'B')) @@ plainto_tsquery(:query) 
    OR LOWER(product.info_name) LIKE :query 
    OR LOWER(product.info_article) LIKE :query) AND product.enable=true
    ORDER BY ts_rank((setweight(to_tsvector('russian', product.info_name), 'A') || setweight(to_tsvector('russian', coalesce('russian', product.info_content,'')), 'B')), plainto_tsquery(:query)) DESC
    LIMIT :limit
)
{$this->selectByWith()}
EOF;

        $statement = $this->connection->prepare($querySearch);
        $statement->bindValue('query', '%' . mb_strtolower($query) . '%');
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::CUSTOM_OBJECT, SearchProduct::class);
    }

    public function searchForBasket(string $query, int $limit = 12): array
    {
        $querySearch = <<<EOF
 WITH porducts_with_price AS(
    {$this->selectForWith()}
    FROM  ecommerce_product product
    WHERE 
    (LOWER(product.info_name) LIKE :query 
    OR LOWER(product.info_article) LIKE :query) AND product.enable=true
    ORDER BY product.info_article ASC
    LIMIT :limit
)
{$this->selectByWith()}
EOF;

        $statement = $this->connection->prepare($querySearch);
        $statement->bindValue('query', '%' . mb_strtolower($query) . '%');
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::CUSTOM_OBJECT, SearchProduct::class);
    }

    public function findRecommendedByPathCategories(string $path = '', int $limit = 6, $orderType = 'random'): array
    {
        $order = 'product.popular ASC';

        if ($orderType === 'random') {
            $order = 'random()';
        }

        $querySearch = <<<EOF
 WITH porducts_with_price AS(
    {$this->selectForWith()}
    FROM  ecommerce_product product, ecommerce_category category, ecommerce_product_category cat_link
    WHERE 
    category.path LIKE :path AND
    category.id=cat_link.category_id AND
    cat_link.product_id=product.id
    GROUP BY product.id
    ORDER BY {$order}
    LIMIT :limit
)
{$this->selectByWith()}
EOF;

        $statement = $this->connection->prepare($querySearch);
        $statement->bindValue('path', mb_strtolower($path) . '%');
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::CUSTOM_OBJECT, SearchProduct::class);
    }

    public function findRecommendedByBasketToken(?string $token, int $limit = 7): array
    {
        if (!$token) {
            return [];
        }


        $querySearch = <<<EOF
 WITH porducts_with_price AS(
    {$this->selectForWith()}
    FROM  ecommerce_product product, ecommerce_product_recommended recommended
    WHERE
    recommended.product_id IN (SELECT basket.product_id FROM basket_items basket WHERE basket.token = :token) 
    AND recommended.recommened_id = product.id
    GROUP BY product.id
    ORDER BY random()
    LIMIT :limit
)
{$this->selectByWith()}
ORDER BY random()
EOF;

        $statement = $this->connection->prepare($querySearch);
        $statement->bindValue('token', $token, ParameterType::STRING);
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::CUSTOM_OBJECT, SearchProduct::class);
    }

    public function findRecommendedByIdProducts(int $id): array
    {
        $querySearch = <<<EOF
 WITH porducts_with_price AS(
    {$this->selectForWithWithPosition()}
    FROM  ecommerce_product product, ecommerce_product_recommended recommended
    WHERE 
    recommended.product_id = :id 
    AND recommended.recommened_id = product.id
    ORDER BY recommended.position ASC
)
{$this->selectByWith()}
ORDER BY product.position ASC
EOF;

        $statement = $this->connection->prepare($querySearch);
        $statement->bindValue('id', $id, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::CUSTOM_OBJECT, SearchProduct::class);
    }

    private function generateWhere(AttributeForFilter $filter, string $field = 'product'): string
    {
        if (Field::NUMBER === $filter->field_type && 'cost' === $filter->slug) {
            $sql = '';
            if ($filter->from) {
                $sql = $field . '.' . $filter->slug . ' >=' . $filter->from;
            }
            if ($filter->to) {
                if (strlen($sql) > 0) {
                    $sql .= ' AND ';
                }
                $sql .= $field . '.' . $filter->slug . ' <=' . $filter->to;
            }
            return ' ( ' . $sql . ') ';
        }

        if (Field::NUMBER === $filter->field_type) {
            $sql = '';
            if ($filter->from) {
                $sql = " (" . $field . ".attributes #>'{" . $filter->slug . ",value}')::float >= " . $filter->from;
            }
            if ($filter->to) {
                if (strlen($sql) > 0) {
                    $sql .= ' AND ';
                }
                $sql .= " (" . $field . ".attributes #>'{" . $filter->slug . ",value}')::float <= " . $filter->to;
            }
            return ' AND ( ' . $sql . ') ';
        }

        if (Field::SELECT === $filter->field_type) {
            return " AND (" . $field . ".attributes @> '{\"" . $filter->slug . "\":{\"value\":\"" . $filter->value . "\"}}') ";
        }

    }

    public function findForSearch(string $path = '', int $limit = 6, int $offset = 0, string $orderField = 'popular', string $orderDirection = 'ASC', array $filters): array
    {
        $order = ' ORDER BY product.' . $orderField . ' ' . $orderDirection;
        $limitOffset = ' LIMIT :limit OFFSET :offset';
        $query = $this->selectByWith();
        $flagCost = false;
        $addingWhere = '';
        $addingWhereCost = '';

        foreach ($filters as $filter) {
            if ('cost' === $filter->slug) {
                $flagCost = true;
                $addingWhereCost = $this->generateWhere($filter);
                continue;
            }
            $addingWhere .= $this->generateWhere($filter);
        }

        if ('cost' === $orderField || $flagCost) {
            $order = '';
            $limitOffset = '';
            $query = $this->selectByWithSort($orderField, $orderDirection, $addingWhereCost);
        }

        $querySearch = <<<EOF
 WITH porducts_with_price AS(
    {$this->selectForWith()}
    FROM  ecommerce_product product, ecommerce_category category, ecommerce_product_category cat_link
    WHERE 
    category.path LIKE :path 
    AND category.id=cat_link.category_id 
    AND cat_link.product_id=product.id
    {$addingWhere}
    GROUP BY product.id
    {$order}
    {$limitOffset}
)
{$query}
EOF;

        $statement = $this->connection->prepare($querySearch);
        $statement->bindValue('path', mb_strtolower($path) . '%');
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $statement->bindValue('offset', $offset, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::CUSTOM_OBJECT, SearchProduct::class);
    }


    private function selectByWith(): string
    {
        return <<<EOF
SELECT product.*, file.filename, file.directory 
FROM porducts_with_price product
LEFT JOIN ecommerce_product_images link ON product.id =link.product_id AND link.cover=true
LEFT JOIN file file ON link.file_id = file.id
EOF;
    }

    private function selectByWithSort(string $field, string $direction, string $where): string
    {
        if (strlen($where) > 0) {
            $where = ' WHERE ' . $where . ' ';
        }
        return <<<EOF
SELECT product.*, file.filename, file.directory 
FROM porducts_with_price product
LEFT JOIN ecommerce_product_images link ON product.id =link.product_id AND link.cover=true
LEFT JOIN file file ON link.file_id = file.id
{$where}
ORDER BY product.{$field} {$direction}
LIMIT :limit OFFSET :offset
EOF;
    }

    private function selectForWith(): string
    {
        return <<<EOF
    SELECT
        product.id,
        product.popular,
        product.info_name as name,
        product.price_old as old_cost,
        product.info_article as article,
        (CASE WHEN product.price_final=true THEN 
            product.price_current
        ELSE
        coalesce((
            SELECT sum(prod.price_current * link.count)
                 FROM ecommerce_product_composition link
                 JOIN ecommerce_product prod ON link.element_id = prod.id
                 WHERE link.product_id = product.id
             ), 0)+product.price_current
        END) as cost
EOF;
    }

    private function selectForWithWithPosition(): string
    {
        return <<<EOF
    SELECT
        product.id,
        product.popular,
        product.info_name as name,
        product.price_old as old_cost,
        product.info_article as article,
        recommended.position as position,
        (CASE WHEN product.price_final=true THEN 
            product.price_current
        ELSE
        coalesce((
            SELECT sum(prod.price_current * link.count)
                 FROM ecommerce_product_composition link
                 JOIN ecommerce_product prod ON link.element_id = prod.id
                 WHERE link.product_id = product.id
             ), 0)+product.price_current
        END) as cost
EOF;
    }
}
