<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
<<<<<<< HEAD
     */
    private $user;
=======
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
<<<<<<< HEAD
    private $Trick;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
=======
    private $trick;
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

<<<<<<< HEAD
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
=======
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d

        return $this;
    }

    public function getTrick(): ?Trick
    {
<<<<<<< HEAD
        return $this->Trick;
    }

    public function setTrick(?Trick $Trick): self
    {
        $this->Trick = $Trick;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
=======
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d

        return $this;
    }
}
