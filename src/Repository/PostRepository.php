<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}
