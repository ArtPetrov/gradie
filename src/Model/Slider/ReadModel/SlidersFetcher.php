<?php

declare(strict_types=1);

namespace App\Model\Slider\ReadModel;

use App\Model\Slider\Entity\Slider;
use App\Model\Slider\Entity\Type;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class SlidersFetcher
{
    private $connection;
    private $paginator;
    private $repository;

    public function __construct(Connection $connection, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->repository = $em->getRepository(Slider::class);
        $this->paginator = $paginator;
    }

    public function getAllByType(Type $type)
    {
        return $this->repository->findBy(['type.type' => $type->getType()], ['position' => 'DESC']);
    }

    public function getAllByTypeEnable(Type $type)
    {
        return $this->repository->findBy(['type.type' => $type->getType(), 'enable' => true], ['position' => 'DESC']);
    }

    public function get(int $id): Slider
    {
        /** @var Slider $slider */
        if (!$slider = $this->repository->find($id)) {
            throw new \DomainException('slider.not.found');
        }
        return $slider;
    }

    public function findByPosition(int $currentPosition, Type $type, string $direction): ?int
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'position')
            ->from('sliders')
            ->andWhere('type = :type')
            ->setParameter('type', $type->getType(), ParameterType::STRING);

        if ($direction === 'up') {
            $stmt->andWhere('position > :position')->orderBy('position', 'ASC');
        } else {
            $stmt->andWhere('position < :position')->orderBy('position', 'DESC');
        }
        $stmt->setParameter('position', $currentPosition, ParameterType::INTEGER);
        $stmt->setMaxResults(1);
        return (int)$stmt->execute()->fetch()['id'];
    }


    public function getRandomContext(int $count = 2): ?array
    {
        $queryContext = <<<EOF
    SELECT 
           slider_header as header,
           slider_description as description,
           button_enable,
           button_label,
           button_link
    FROM  sliders s
    WHERE 
    s.type = 'context' AND
    s.enable = true
    ORDER BY random()
    LIMIT :limit
EOF;
        $statement = $this->connection->prepare($queryContext);
        $statement->bindValue('limit', $count, ParameterType::INTEGER);
        $statement->execute();
        return $statement->fetchAll(FetchMode::STANDARD_OBJECT);
    }

}
