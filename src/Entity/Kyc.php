<?php

namespace App\Entity;

use App\Repository\KycRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=KycRepository::class)
 */
class Kyc
{
    public const STATUS_PENDING = 'en_attente';
    public const STATUS_VALIDATED = 'validé';
    public const STATUS_REJECTED = 'rejeté';

    public const TYPE_CNI = 'cni';
    public const TYPE_PASSEPORT = 'passeport';
    public const TYPE_PERMIS = 'permis_conduire';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="kycs")
     * @ORM\JoinColumn(name="id_utilisateur", referencedColumnName="id")
     */
    private $utilisateur;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $typePiece;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $numeroPiece;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoPieceRecto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoPieceVerso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoSelfie;

    /**
     * @ORM\Column(type="string", length=20, options={"default"="en_attente"})
     */
    private $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motif;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function getTypePiece(): ?string
    {
        return $this->typePiece;
    }

    public function setTypePiece(string $typePiece): self
    {
        $this->typePiece = $typePiece;

        return $this;
    }

    public function getNumeroPiece(): ?string
    {
        return $this->numeroPiece;
    }

    public function setNumeroPiece(string $numeroPiece): self
    {
        $this->numeroPiece = $numeroPiece;

        return $this;
    }

    public function getPhotoPieceRecto(): ?string
    {
        return $this->photoPieceRecto;
    }

    public function setPhotoPieceRecto(?string $photoPieceRecto): self
    {
        $this->photoPieceRecto = $photoPieceRecto;

        return $this;
    }

    public function getPhotoPieceVerso(): ?string
    {
        return $this->photoPieceVerso;
    }

    public function setPhotoPieceVerso(?string $photoPieceVerso): self
    {
        $this->photoPieceVerso = $photoPieceVerso;

        return $this;
    }

    public function getPhotoSelfie(): ?string
    {
        return $this->photoSelfie;
    }

    public function setPhotoSelfie(?string $photoSelfie): self
    {
        $this->photoSelfie = $photoSelfie;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}

