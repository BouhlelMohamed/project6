<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
<<<<<<< HEAD
    private $nom;

    /**
     * @ORM\Column(type="text", nullable=true)
=======
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d
     */
    private $description;

    /**
<<<<<<< HEAD
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="Trick")
=======
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick")
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
=======
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

<<<<<<< HEAD
    public function setDescription(?string $description): self
=======
    public function setDescription(string $description): self
>>>>>>> e5894879c9bfff14901baeef334188b56858ff5d
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }
}
