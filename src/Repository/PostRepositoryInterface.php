<?php


namespace App\Repository;


use App\Entity\Post;

interface PostRepositoryInterface
{
    /**
     * @return Post[]
     */
    public function getAllPost(): array;

    /**
     * @param int $postId
     * @return Post
     */
    public function getOnePost(int $postId): object;

    /**
     * @param Post $post
     * @return Post
     */
    public function setCreatePost(Post $post): object;

    /**
     * @param Post $post
     * @return Post
     */
    public function setUpdatePost(Post $post): object;

    /**
     * @param Post $post
     */
    public function setDeletePost(Post $post);
}