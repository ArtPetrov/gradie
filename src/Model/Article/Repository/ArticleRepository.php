<?php

declare(strict_types=1);

namespace App\Model\Article\Repository;

use App\Model\Article\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class ArticleRepository
{
    private $em;
    private $connection;
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->repo = $this->em->getRepository(Article::class);
    }

    public function add(Article $news): void
    {
        $this->em->persist($news);
    }

    public function get(int $id): Article
    {
        /** @var Article $news */
        if (!$news = $this->repo->find($id)) {
            throw new \DomainException('article.not.found');
        }
        return $news;
    }

    public function getAll()
    {
        return $this->repo->findBy([],['publishedAt'=>'DESC']);
    }

    public function remove(Article $album): void
    {
        $this->em->remove($album);
    }
}
