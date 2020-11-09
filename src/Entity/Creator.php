<?php

namespace App\Entity;

use App\Repository\CreatorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CreatorRepository::class)
 */
class Creator
{
    public const PUBLISHED= 1;
    public const DRAFT = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $surname;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $participation;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_at;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="creator")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished()
    {
        $this->is_published = self::PUBLISHED;
    }

    public function setIsDraft()
    {
        $this->is_published = self::DRAFT;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAtValue()
    {
        $this->create_at = new \DateTime();
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->update_at;
    }

    public function setUpdateAtValue()
    {
        $this->update_at = new \DateTime();
    }

    public function setUpdateAt(\DateTimeInterface $update_at): self
    {
        $this->update_at = $update_at;

        return $this;
    }

    public function getParticipation(): ?string
    {
        return $this->participation;
    }

    public function setParticipation(string $participation): self
    {
        $this->participation = $participation;

        return $this;
    }


//    public function __toString()
//    {
//     return $this->name;
//    }
    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return Creator
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @param Post $post
     */
    public function removePost(Post $post)
    {
        $this->post->removeElement($post);
    }
}
