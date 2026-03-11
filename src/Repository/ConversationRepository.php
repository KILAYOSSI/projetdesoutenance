<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * Find all conversations for a user, ordered by last message
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.participant1 = :user OR c.participant2 = :user')
            ->setParameter('user', $user)
            ->orderBy('c.lastMessageAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find conversation between two users
     */
    public function findConversationBetweenUsers(User $user1, User $user2): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('(c.participant1 = :user1 AND c.participant2 = :user2) OR (c.participant1 = :user2 AND c.participant2 = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get total unread messages for a user
     */
    public function getTotalUnreadForUser(User $user): int
    {
        $conversations = $this->findByUser($user);
        $total = 0;
        
        foreach ($conversations as $conversation) {
            $total += $conversation->getUnreadCountForUser($user);
        }
        
        return $total;
    }
}


