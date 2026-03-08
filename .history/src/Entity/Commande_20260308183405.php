<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 * @ORM\Table(name="commande")
 */
class Commande
{
    public const STATUS_PENDING = 'en_attente';
    public const STATUS_CONFIRMED = 'confirme';
    public const STATUS_PROCESSING = 'en_cours';
    public const STATUS_DELIVERED = 'livré';
    public const STATUS_CANCELLED = 'annulé';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     * @ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="commande", cascade={"persist", "remove"})
     */
    private $ligneCommandes;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $montantTotal = 0;

    /**
     * @ORM\Column(type="string", length=20, options={"default"="en_attente"})
     */
    private $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCommande;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
        $this->dateCommande = new \DateTime();
        $this->montantTotal = 0;
        $this->status = self::STATUS_PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes[] = $ligneCommande;
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    /**
     * Alias pour getMontantTotal() - utilisé dans les templates
     */
    public function getTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): self
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Calcule automatiquement le montant total à partir des lignes de commande
     */
    public function calculateMontantTotal(): void
    {
        $total = 0;
        foreach ($this->ligneCommandes as $ligne) {
            $total += $ligne->getQuantite() * $ligne->getPrixUnitaire();
        }
        $this->montantTotal = $total;
    }
}

