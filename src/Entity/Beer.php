<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BeerRepository")
 */
class Beer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brewery", inversedBy="beers")
     */
    private $brewery;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="beers")
     */
    private $cat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Style", inversedBy="beers")
     */
    private $style;

    /**
     * @ORM\Column(type="float")
     */
    private $abv;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ibu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $srm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filepath;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descript;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $add_user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_mod;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $upc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrewery(): ?Brewery
    {
        return $this->brewery;
    }

    public function setBrewery(?Brewery $brewery): self
    {
        $this->brewery = $brewery;

        return $this;
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

    public function getCat(): ?Category
    {
        return $this->cat;
    }

    public function setCat(?Category $cat): self
    {
        $this->cat = $cat;

        return $this;
    }

    public function getStyle(): ?Style
    {
        return $this->style;
    }

    public function setStyle(?Style $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getAbv(): ?float
    {
        return $this->abv;
    }

    public function setAbv(float $abv): self
    {
        $this->abv = $abv;

        return $this;
    }

    public function getIbu(): ?int
    {
        return $this->ibu;
    }

    public function setIbu(?int $ibu): self
    {
        $this->ibu = $ibu;

        return $this;
    }

    public function getSrm(): ?int
    {
        return $this->srm;
    }

    public function setSrm(?int $srm): self
    {
        $this->srm = $srm;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(?string $filepath): self
    {
        $this->filepath = $filepath;

        return $this;
    }

    public function getDescript(): ?string
    {
        return $this->descript;
    }

    public function setDescript(?string $descript): self
    {
        $this->descript = $descript;

        return $this;
    }

    public function getAddUser(): ?int
    {
        return $this->add_user;
    }

    public function setAddUser(?int $add_user): self
    {
        $this->add_user = $add_user;

        return $this;
    }

    public function getLastMod(): ?\DateTimeInterface
    {
        return $this->last_mod;
    }

    public function setLastMod(?\DateTimeInterface $last_mod): self
    {
        $this->last_mod = $last_mod;

        return $this;
    }

    public function getUpc(): ?int
    {
        return $this->upc;
    }

    public function setUpc(?int $upc): self
    {
        $this->upc = $upc;

        return $this;
    }
}
