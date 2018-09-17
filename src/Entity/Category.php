<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
    private $cat_name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_mod;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Beer", mappedBy="cat")
     */
    private $beers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Style", mappedBy="cat")
     */
    private $styles;

    public function __construct()
    {
        $this->beers = new ArrayCollection();
        $this->styles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCatName(): ?string
    {
        return $this->cat_name;
    }

    public function setCatName(string $cat_name): self
    {
        $this->cat_name = $cat_name;

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

    /**
     * @return Collection|Beer[]
     */
    public function getBeers(): Collection
    {
        return $this->beers;
    }

    public function addBeer(Beer $beer): self
    {
        if (!$this->beers->contains($beer)) {
            $this->beers[] = $beer;
            $beer->setCat($this);
        }

        return $this;
    }

    public function removeBeer(Beer $beer): self
    {
        if ($this->beers->contains($beer)) {
            $this->beers->removeElement($beer);
            // set the owning side to null (unless already changed)
            if ($beer->getCat() === $this) {
                $beer->setCat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Style[]
     */
    public function getStyles(): Collection
    {
        return $this->styles;
    }

    public function addStyle(Style $style): self
    {
        if (!$this->styles->contains($style)) {
            $this->styles[] = $style;
            $style->setCat($this);
        }

        return $this;
    }

    public function removeStyle(Style $style): self
    {
        if ($this->styles->contains($style)) {
            $this->styles->removeElement($style);
            // set the owning side to null (unless already changed)
            if ($style->getCat() === $this) {
                $style->setCat(null);
            }
        }

        return $this;
    }
}
