<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Find messages by conversation
     */
    public function findByConversation(Conversation $conversation): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Mark all messages as read in a conversation for a user
     */
    public function markAllAsRead(Conversation $conversation, int $userId): void
    {
        $this->createQueryBuilder('m')
            ->update(Message::class, 'm')
            ->set('m.isRead', 'true')
            ->where('m.conversation = :conversation')
            ->andWhere('m.sender != :userId')
            ->setParameter('conversation', $conversation)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    /**
     * Count unread messages for a user across all conversations
     */
    public function countUnreadForUser(int $userId): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.sender != :userId')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('userId', $userId)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}


