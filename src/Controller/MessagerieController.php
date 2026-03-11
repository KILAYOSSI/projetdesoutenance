<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MessagerieController extends AbstractController
{
    #[Route('/messagerie', name: 'messagerie_index')]
    public function index(ConversationRepository $conversationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $conversations = $conversationRepository->findByUser($user);

        return $this->render('messagerie/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    #[Route('/messagerie/{id}', name: 'messagerie_conversation', requirements: ['id' => '\d+'])]
    public function conversation(
        int $id,
        ConversationRepository $conversationRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $conversation = $conversationRepository->find($id);
        
        if (!$conversation || !$conversation->hasParticipant($user)) {
            throw $this->createNotFoundException('Conversation non trouvée');
        }

        // Mark messages as read
        $messageRepository->markAllAsRead($conversation, $user->getId());

        $messages = $messageRepository->findByConversation($conversation);
        $otherUser = $conversation->getOtherParticipant($user);

        return $this->render('messagerie/conversation.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherUser' => $otherUser,
        ]);
    }

    #[Route('/messagerie/start/{userId}', name: 'messagerie_start', requirements: ['userId' => '\d+'])]
    public function startConversation(
        int $userId,
        ConversationRepository $conversationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        $otherUser = $entityManager->getRepository(User::class)->find($userId);
        
        if (!$otherUser) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Check if conversation already exists
        $conversation = $conversationRepository->findConversationBetweenUsers($currentUser, $otherUser);
        
        if (!$conversation) {
            // Create new conversation
            $conversation = new Conversation();
            $conversation->setParticipant1($currentUser);
            $conversation->setParticipant2($otherUser);
            $conversation->setCreatedAt(new \DateTime());
            $entityManager->persist($conversation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('messagerie_conversation', ['id' => $conversation->getId()]);
    }

    #[Route('/messagerie/send/{conversationId}', name: 'messagerie_send', requirements: ['conversationId' => '\d+'], methods: ['POST'])]
    public function sendMessage(
        int $conversationId,
        Request $request,
        ConversationRepository $conversationRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $conversation = $conversationRepository->find($conversationId);
        
        if (!$conversation || !$conversation->hasParticipant($user)) {
            return new JsonResponse(['error' => 'Conversation non trouvée'], 404);
        }

        $content = $request->request->get('content', '');
        
        // Block phone numbers and emails
        if (preg_match('/(\+?\d{8,15}|\d{3,4}[\s\-\.]?\d{3,4}[\s\-\.]?\d{3,4})/', $content) || 
            preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $content)) {
            return new JsonResponse(['error' => 'Les numéros de téléphone et emails ne sont pas autorisés'], 400);
        }

        if (empty(trim($content))) {
            return new JsonResponse(['error' => 'Le message ne peut pas être vide'], 400);
        }

        // Create message
        $message = new Message();
        $message->setConversation($conversation);
        $message->setSender($user);
        $message->setContent(htmlspecialchars($content));
        $message->setCreatedAt(new \DateTime());
        $message->setIsRead(false);

        $entityManager->persist($message);
        
        // Update conversation last message time
        $conversation->setLastMessageAt(new \DateTime());
        
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'sender' => $user->getNomComplet() ?: $user->getEmail(),
                'createdAt' => $message->getCreatedAt()->format('H:i'),
                'isMine' => true
            ]
        ]);
    }

    #[Route('/messagerie/api/conversations', name: 'messagerie_api_conversations')]
    public function apiConversations(ConversationRepository $conversationRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $conversations = $conversationRepository->findByUser($user);
        
        $data = [];
        foreach ($conversations as $conversation) {
            $otherUser = $conversation->getOtherParticipant($user);
            $lastMessage = $conversation->getLastMessage();
            $unreadCount = $conversation->getUnreadCountForUser($user);
            
            $data[] = [
                'id' => $conversation->getId(),
                'otherUser' => [
                    'id' => $otherUser->getId(),
                    'name' => $otherUser->getNomComplet() ?: $otherUser->getEmail(),
                ],
                'lastMessage' => $lastMessage ? $lastMessage->getContent() : null,
                'lastMessageAt' => $conversation->getLastMessageAt() ? $conversation->getLastMessageAt()->format('d/m H:i') : null,
                'unreadCount' => $unreadCount,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/messagerie/api/messages/{conversationId}', name: 'messagerie_api_messages')]
    public function apiMessages(
        int $conversationId,
        ConversationRepository $conversationRepository,
        MessageRepository $messageRepository
    ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $conversation = $conversationRepository->find($conversationId);
        
        if (!$conversation || !$conversation->hasParticipant($user)) {
            return new JsonResponse(['error' => 'Conversation non trouvée'], 404);
        }

        // Mark as read
        $messageRepository->markAllAsRead($conversation, $user->getId());

        $messages = $messageRepository->findByConversation($conversation);
        
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'sender' => $message->getSender()->getNomComplet() ?: $message->getSender()->getEmail(),
                'senderId' => $message->getSender()->getId(),
                'createdAt' => $message->getCreatedAt()->format('H:i'),
                'isMine' => $message->getSender()->getId() === $user->getId(),
                'isRead' => $message->isIsRead(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/messagerie/message/{messageId}/edit', name: 'messagerie_edit_message', requirements: ['messageId' => '\d+'], methods: ['POST'])]
    public function editMessage(
        int $messageId,
        Request $request,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $message = $messageRepository->find($messageId);
        
        if (!$message) {
            return new JsonResponse(['error' => 'Message non trouvé'], 404);
        }

        // Only sender can edit their message
        if ($message->getSender()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas modifier ce message'], 403);
        }

        $newContent = $request->request->get('content', '');
        
        // Block phone numbers and emails
        if (preg_match('/(\+?\d{8,15}|\d{3,4}[\s\-\.]?\d{3,4}[\s\-\.]?\d{3,4})/', $newContent) || 
            preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $newContent)) {
            return new JsonResponse(['error' => 'Les numéros de téléphone et emails ne sont pas autorisés'], 400);
        }

        if (empty(trim($newContent))) {
            return new JsonResponse(['error' => 'Le message ne peut pas être vide'], 400);
        }

        // Update message
        $message->setContent(htmlspecialchars($newContent));
        $message->setEdited(true);
        $message->setEditedAt(new \DateTime());
        
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'isEdited' => true,
                'editedAt' => $message->getEditedAt()->format('H:i')
            ]
        ]);
    }

    #[Route('/messagerie/message/{messageId}/delete', name: 'messagerie_delete_message', requirements: ['messageId' => '\d+'], methods: ['POST'])]
    public function deleteMessage(
        int $messageId,
        Request $request,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $message = $messageRepository->find($messageId);
        
        if (!$message) {
            return new JsonResponse(['error' => 'Message non trouvé'], 404);
        }

        // Only sender can delete their message
        if ($message->getSender()->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Vous ne pouvez pas supprimer ce message'], 403);
        }

        $deleteForEveryone = $request->request->get('deleteForEveryone', false) === 'true';
        
        if ($deleteForEveryone) {
            // Delete for everyone
            $entityManager->remove($message);
        } else {
            // Soft delete (only for sender)
            $message->setDeleted(true);
            $message->setDeletedForEveryone(false);
            $message->setContent('Ce message a été supprimé');
        }
        
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'deleted' => true,
            'deleteForEveryone' => $deleteForEveryone
        ]);
    }
}


