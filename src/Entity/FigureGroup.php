<?php

namespace App\Entity;

use App\Repository\FigureGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FigureGroupRepository::class)
 */
class FigureGroup
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Tricks::class, mappedBy="figureGroup", orphanRemoval=true)
     */
    private $trick;

    public function __construct()
    {
        $this->trick = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Tricks>
     */
    public function getTrick(): Collection
    {
        return $this->trick;
    }

    public function addTrick(Tricks $trick): self
    {
        if (!$this->trick->contains($trick)) {
            $this->trick[] = $trick;
            $trick->setFigureGroup($this);
        }

        return $this;
    }

    public function removeTrick(Tricks $trick): self
    {
        if ($this->trick->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getFigureGroup() === $this) {
                $trick->setFigureGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }
}
