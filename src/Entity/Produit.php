<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Produit
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
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $soustitre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $couverture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_3;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="produits")
     */
    private $categorie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isBest;

    public function __construct()
    {
        $this->categorie = new ArrayCollection();
    }

    /**
     * Initialise le slug
     * 
     *@ORM\PrePersist
     *@ORM\PreUpdate
     *
     * @return void
     */
    public function initializeSlug() {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->titre);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSoustitre(): ?string
    {
        return $this->soustitre;
    }

    public function setSoustitre(string $soustitre): self
    {
        $this->soustitre = $soustitre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCouverture(): ?string
    {
        return $this->couverture;
    }

    public function setCouverture(string $couverture): self
    {
        $this->couverture = $couverture;

        return $this;
    }

    public function getImage1(): ?string
    {
        return $this->image_1;
    }

    public function setImage1(string $image_1): self
    {
        $this->image_1 = $image_1;

        return $this;
    }

    public function getImage2(): ?string
    {
        return $this->image_2;
    }

    public function setImage2(string $image_2): self
    {
        $this->image_2 = $image_2;

        return $this;
    }

    public function getImage3(): ?string
    {
        return $this->image_3;
    }

    public function setImage3(string $image_3): self
    {
        $this->image_3 = $image_3;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    public function getIsBest(): ?bool
    {
        return $this->isBest;
    }

    public function setIsBest(?bool $isBest): self
    {
        $this->isBest = $isBest;

        return $this;
    }
}
