<?php

namespace App\Entity;

use App\Repository\TricksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TricksRepository::class)
 * @UniqueEntity(fields="Name", message="Cette figure existe déjà.")
 */
class Tricks
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Ce champ ne peut être vide.")
     * @Assert\Length(min=3, max=30,
     * minMessage="Votre pseudo doit contenir au moins 3 caractères.",
     * maxMessage="Votre pseudo ne peut contenir plus de 30 caractères.")
     */
    private $Name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Ce champ ne peut être vide.")
     * @Assert\Length(min=10,
     * minMessage="La description doit contenir au moins 10 caractères")
     */
    private $Description;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $Created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $Modified_at;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Users;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="tricks", cascade={"persist"}, orphanRemoval=true)
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mainMedia;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="Tricks", cascade={"persist"}, orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=FigureGroup::class, inversedBy="trick", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $figureGroup;

    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="trick", cascade={"persist"}, orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Slug;


    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

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

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->Modified_at;
    }

    public function setModifiedAt(\DateTimeImmutable $Modified_at): self
    {
        $this->Modified_at = $Modified_at;

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

    public function getSlug(): ?string
    {
        return $this->Slug;
    }

    public function setSlug(string $Slug): self
    {
        $this->Slug = $Slug;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setTricks($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): self
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getTricks() === $this) {
                $medium->setTricks(null);
            }
        }

        return $this;
    }

    public function getMainMedia(): ?string
    {
        return $this->mainMedia;
    }

    public function setMainMedia(?string $mainMedia): self
    {
        $this->mainMedia = $mainMedia;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTricks($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTricks() === $this) {
                $comment->setTricks(null);
            }
        }

        return $this;
    }

    public function getFigureGroup(): ?FigureGroup
    {
        return $this->figureGroup;
    }

    public function setFigureGroup(?FigureGroup $figureGroup): self
    {
        $this->figureGroup = $figureGroup;

        return $this;
    }

    /**
     * @return Collection<int, Videos>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideos(Videos $videos): self
    {
        if (!$this->videos->contains($videos)) {
            $this->videos[] = $videos;
            $videos->setTrick($this);
        }

        return $this;
    }

    public function removeVideos(Videos $videos): self
    {
        if ($this->videos->removeElement($videos)) {
            // set the owning side to null (unless already changed)
            if ($videos->getTrick() === $this) {
                $videos->setTrick(null);
            }
        }

        return $this;
    }
}
