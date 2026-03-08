<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    public const STATUS_PENDING = 'en_attente';
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
     * @ORM\ManyToMany(targetEntity=Produit::class, inversedBy="commandes")
     * @ORM\JoinTable(name="ligne_commande",
     *      joinColumns={@ORM\JoinColumn(name="commande_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="produit_id", referencedColumnName="id")}
     * )
     */
    private $produits;

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
        $this->produits = new ArrayCollection();
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
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->addCommande($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeCommande($this);
        }

        return $this;
    }

    public function getMontantTotal(): ?string
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
}

