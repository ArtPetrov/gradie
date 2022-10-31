<?php

declare(strict_types=1);

namespace App\Model\Slider\Repository;

use App\Model\Slider\Entity\Slider;
use Doctrine\ORM\EntityManagerInterface;

class SlidersRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Slider::class);
    }

    public function add(Slider $slider): void
    {
        $this->em->persist($slider);
    }

    public function get(int $id): Slider
    {
        /** @var Slider $slider */
        if (!$slider = $this->repo->find($id)) {
            throw new \DomainException('slider.not.found');
        }
        return $slider;
    }

    public function remove(Slider $slider): void
    {
        $this->em->remove($slider);
    }
}
