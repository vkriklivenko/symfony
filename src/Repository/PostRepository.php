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
    public function findPosts($term): array
    {
        $terms = explode(" ", $term);

        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.creator', 'c')
            ->addSelect('c')
        ;
        $qb = $qb->andWhere($qb->expr()->orX(
            $qb->expr()->in('a.title', ":term"),
            $qb->expr()->in('a.year', ":term"),
            $qb->expr()->in('a.numOfPoints', ":term"),
            $qb->expr()->in('a.conference', ":term"),
            $qb->expr()->in('c.user', ":term"),
            $qb->expr()->in('c.participation', ":term")
        ));
        $qb->setParameter('term', $terms);

        return $qb->getQuery()->execute();
    }

    /**
     * @param null $filters
     * @return array|mixed
     */
    public function findAllPosts($filters = null)
    {
        $db = $this->createQueryBuilder('a');
        if (count($filters)) {
            $this->applyFilters($db, $filters);
        }

        return $db->getQuery()->getResult();
    }

    public function findAllToExport($columns, $rows = null, $filters = [])
    {
        $query = $this->createQueryBuilder('a')->select($columns)->leftJoin('App:Creator', 'c', 'WITH', 'c.id = a.id');

        if ($rows){
            $query->where("a.id IN (". $rows . ")");
        }

        if (count($filters)) {
            $this->applyFilters($query, $filters);
        }

        return $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    }

    protected function applyFilters($queryBuilder, $filters)
    {
        if (count($filters)) {
            foreach ($filters as $filter => $value) {
                if ($value) {
                    switch ($filter) {
                        case 'keyword':
                            $queryBuilder->andWhere('a.title LIKE :keyword')
                                ->setParameter('keyword', '%' . $value . '%');
                            break;
                        case 'from':
                            $date = date("Y-m-d H:i:s", strtotime($value . '-01-01 00:00:00'));
                            $queryBuilder->andWhere('a.year >= :from')
                                ->setParameter('from', $date);
                            break;
                        case 'to':
                            $date = date("Y-m-d H:i:s", strtotime($value . '-12-31 23:59:59'));
                            $queryBuilder->andWhere('a.year <= :to')
                                ->setParameter('to', $date);
                            break;
                        case 'num_of_points':
                            $queryBuilder->andWhere('a.numOfPoints = :points')
                                ->setParameter('points', $value);
                            break;
                    }
                }
            }
        }
    }
}
