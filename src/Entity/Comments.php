<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 */
class Comments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=3,
     * minMessage="Votre message doit contenir au moins 3 caractères")
     */
    private $Content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $Created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Users;

    /**
     * @ORM\ManyToOne(targetEntity=Tricks::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Tricks;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): self
    {
        $this->Content = $Content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->Created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $Created_at): self
    {
        $this->Created_at = $Created_at;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->Users;
    }

    public function setUsers(?Users $Users): self
    {
        $this->Users = $Users;

        return $this;
    }

    public function getTricks(): ?Tricks
    {
        return $this->Tricks;
    }

    public function setTricks(?Tricks $Tricks): self
    {
        $this->Tricks = $Tricks;

        return $this;
    }
}
