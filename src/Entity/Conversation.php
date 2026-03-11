<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $participant1;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $participant2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="conversation", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $messages;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastMessageAt;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant1(): ?User
    {
        return $this->participant1;
    }

    public function setParticipant1(?User $participant1): self
    {
        $this->participant1 = $participant1;
        return $this;
    }

    public function getParticipant2(): ?User
    {
        return $this->participant2;
    }

    public function setParticipant2(?User $participant2): self
    {
        $this->participant2 = $participant2;
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

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }
        return $this;
    }

    public function getLastMessageAt(): ?\DateTimeInterface
    {
        return $this->lastMessageAt;
    }

    public function setLastMessageAt(?\DateTimeInterface $lastMessageAt): self
    {
        $this->lastMessageAt = $lastMessageAt;
        return $this;
    }

    /**
     * Get the other participant in the conversation
     */
    public function getOtherParticipant(User $currentUser): ?User
    {
        if ($this->participant1->getId() === $currentUser->getId()) {
            return $this->participant2;
        }
        return $this->participant1;
    }

    /**
     * Check if user is part of this conversation
     */
    public function hasParticipant(User $user): bool
    {
        return ($this->participant1->getId() === $user->getId() || $this->participant2->getId() === $user->getId());
    }

    /**
     * Get unread messages count for a user
     */
    public function getUnreadCountForUser(User $user): int
    {
        $count = 0;
        foreach ($this->messages as $message) {
            if ($message->getSender()->getId() !== $user->getId() && !$message->isIsRead()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get last message
     */
    public function getLastMessage(): ?Message
    {
        return $this->messages->last() ?: null;
    }
}

