<?php

namespace App\Repository;

use App\Entity\Creator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Creator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creator[]    findAll()
 * @method Creator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreatorRepository extends ServiceEntityRepository implements CreatorRepositoryInterface
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct($registry, Creator::class);
    }

    public function getAllCreator(): array
    {
        return parent::findAll();
    }

    public function getOneCreator(int $creatorId): object
    {
        return parent::find($creatorId);
    }

    public function setCreateCreator(Creator $creator): object
    {
        $creator->setCreateAtValue();
        $creator->setUpdateAtValue();
        $creator->setIsPublished();
        $this->manager->persist($creator);
        $this->manager->flush();
        return $creator;
    }

    /**
     * @inheritDoc
     */
    public function setUpdateCreator(Creator $creator): object
    {
        $creator->setUpdateAtValue();
        $this->manager->flush();
        return $creator;
    }

    /**
     * @inheritDoc
     */
    public function setDeleteCreator(Creator $creator)
    {
        $this->manager->remove($creator);
        $this->manager->flush();
    }

    /**
     * @param $term
     * @return Creator[]
     */
    public function findCreators($term)
    {
        $terms = explode(" ", $term);

        $qb = $this->createQueryBuilder('c')
            ->select('c a')
            ->leftJoin('c.id', 'a')
        ;
        $qb = $qb->andWhere($qb->expr()->orX(
            $qb->expr()->in('c.user', ":term"),
            $qb->expr()->in('c.participation', ":term")
        ));
        $qb->setParameter('term', $terms);

        return $qb->getQuery()->execute();
    }
}
