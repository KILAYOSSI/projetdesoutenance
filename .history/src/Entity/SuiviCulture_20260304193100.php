<?php

namespace App\Entity;

use App\Repository\SuiviCultureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuiviCultureRepository::class)
 */
class SuiviCulture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Exploitation::class, inversedBy="suiviCultures")
     * @ORM\JoinColumn(name="exploitation_id", referencedColumnName="id")
     */
    private $exploitation;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class)
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $variete;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $superficie;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateSemis;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateRecoltePrevue;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateRecolteReelle;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observations;

    /**
     * @ORM\OneToMany(targetEntity=Rendement::class, mappedBy="suiviCulture")
     */
    private $rendements;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->rendements = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->statut = 'en_cours';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExploitation(): ?Exploitation
    {
        return $this->exploitation;
    }

    public function setExploitation(?Exploitation $exploitation): self
    {
        $this->exploitation = $exploitation;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVariete(): ?string
    {
        return $this->variete;
    }

    public function setVariete(?string $variete): self
    {
        $this->variete = $variete;

        return $this;
    }

    public function getSuperficie(): ?string
    {
        return $this->superficie;
    }

    public function setSuperficie(?string $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getDateSemis(): ?\DateTimeInterface
    {
        return $this->dateSemis;
    }

    public function setDateSemis(?\DateTimeInterface $dateSemis): self
    {
        $this->dateSemis = $dateSemis;

        return $this;
    }

    public function getDateRecoltePrevue(): ?\DateTimeInterface
    {
        return $this->dateRecoltePrevue;
    }

    public function setDateRecoltePrevue(?\DateTimeInterface $dateRecoltePrevue): self
    {
        $this->dateRecoltePrevue = $dateRecoltePrevue;

        return $this;
    }

    public function getDateRecolteReelle(): ?\DateTimeInterface
    {
        return $this->dateRecolteReelle;
    }

    public function setDateRecolteReelle(?\DateTimeInterface $dateRecolteReelle): self
    {
        $this->dateRecolteReelle = $dateRecolteReelle;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return Collection|Rendement[]
     */
    public function getRendements(): Collection
    {
        return $this->rendements;
    }

    public function addRendement(Rendement $rendement): self
    {
        if (!$this->rendements->contains($rendement)) {
            $this->rendements[] = $rendement;
            $rendement->setSuiviCulture($this);
        }

        return $this;
    }

    public function removeRendement(Rendement $rendement): self
    {
        if ($this->rendements->removeElement($rendement)) {
            if ($rendement->getSuiviCulture() === $this) {
                $rendement->setSuiviCulture(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Calculate total yield for this culture
     */
    public function getTotalRendement(): float
    {
        $total = 0;
        foreach ($this->rendements as $rendement) {
            if ($rendement->getQuantite()) {
                $total += (float) $rendement->getQuantite();
            }
        }
        return $total;
    }

    /**
     * Calculate yield per hectare
     */
    public function getRendementHectare(): ?float
    {
        if (!$this->superficie || (float)$this->superficie === 0) {
            return null;
        }
        return $this->getTotalRendement() / (float) $this->superficie;
    }
}
