<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity=Tricks::class, inversedBy="media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tricks;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTricks(): ?Tricks
    {
        return $this->tricks;
    }

    public function setTricks(?Tricks $tricks): self
    {
        $this->tricks = $tricks;

        return $this;
    }
}
