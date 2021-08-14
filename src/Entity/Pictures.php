<?php

namespace App\Entity;

use App\Repository\PicturesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PicturesRepository::class)
 */
class Pictures
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
    private $Name;

    /**
     * @ORM\Column(type="integer")
     */
    private $Lenght;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Description;

    /**
     * @ORM\Column(type="blob")
     */
    private $ImgBlob;

    /**
     * @ORM\ManyToOne(targetEntity=Tricks::class, inversedBy="pictures")
     */
    private $Tricks;

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

    public function getLenght(): ?int
    {
        return $this->Lenght;
    }

    public function setLenght(int $Lenght): self
    {
        $this->Lenght = $Lenght;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

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

    public function getImgBlob()
    {
        return $this->ImgBlob;
    }

    public function setImgBlob($ImgBlob): self
    {
        $this->ImgBlob = $ImgBlob;

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
