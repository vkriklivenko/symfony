<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements PostRepositoryInterface
{

    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct($registry, Post::class);
    }

    public function getAllPost(): array
    {
        return parent::findAll();
    }

    public function getOnePost(int $postId): object
    {
        return parent::find($postId);
    }

    public function setCreatePost(Post $post): object
    {
        $post->setCreateAtValue();
        $post->setUpdateAtValue();
        $post->setIsPublished();
        $this->manager->persist($post);
        $this->manager->flush();
        return $post;
    }

    public function setUpdatePost(Post $post): object
    {
        $post->setUpdateAtValue();
        $this->manager->flush();
        return $post;
    }

    public function setDeletePost(Post $post)
    {
        $this->manager->remove($post);
        $this->manager->flush();
    }

    /**
     * @param $term
     * @return Post[]
     */
    public function findCreators($term): array
    {
        $terms = explode(" ", $term);

        $qb = $this->createQueryBuilder('a')
            ->select('a c')
            ->leftJoin('a.creator', 'c')
        ;
        $qb = $qb->andWhere($qb->expr()->orX(
            $qb->expr()->in('a.title', ":term"),
            $qb->expr()->in('a.year', ":term"),
            $qb->expr()->in('a.numOfPoints', ":term"),
            $qb->expr()->in('a.conference', ":term")
        ));
        $qb->setParameter('term', $terms);

        return $qb->getQuery()->execute();
    }
}
