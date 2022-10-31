<?php

declare(strict_types=1);

namespace App\Model\Page\Repository;

use App\Model\Page\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;

class PageRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Page::class);
    }

    public function add(Page $page): void
    {
        $this->em->persist($page);
    }

    public function get(int $id): Page
    {
        /** @var Page $page */
        if (!$page = $this->repo->find($id)) {
            throw new \DomainException('page.not.found');
        }
        return $page;
    }

    public function getBySlug(string $slug): Page
    {
        /** @var Page $page */
        if (!$page = $this->repo->findOneBy(['settings.slug' => $slug])) {
            throw new \DomainException('page.not.found');
        }
        return $page;
    }

    public function getAll()
    {
        return $this->repo->findBy([],['name'=>'ASC']);
    }

    public function remove(Page $page): void
    {
        $this->em->remove($page);
    }

}
