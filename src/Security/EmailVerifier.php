<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EmailVerifier
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        MailerInterface $mailer, 
        EntityManagerInterface $manager,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->entityManager = $manager;
        $this->logger = $logger;
    }

    /**
     * Generate and send a confirmation code via email
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('User must be instance of App\Entity\User');
        }

        try {
            // Generate a 6-digit confirmation code
            $confirmationCode = sprintf('%06d', random_int(0, 999999));
            
            $this->logger->info('Génération du code de confirmation', [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
                'code' => $confirmationCode
            ]);

            // Set the code and expiration (15 minutes)
            $user->setConfirmationCode($confirmationCode);
            $user->setCodeExpiresAt(new \DateTime('+15 minutes'));
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->logger->info('Code de confirmation sauvegardé en base de données');

            // Add the code to the email context
            $context = $email->getContext();
            $context['confirmationCode'] = $confirmationCode;
            if (!isset($context['expires_in'])) {
                $context['expires_in'] = '15 minutes';
            }
            $email->context($context);

            // Send the email
            $this->mailer->send($email);
            
            $this->logger->info('Email de confirmation envoyé avec succès', [
                'to' => $user->getEmail(),
                'code' => $confirmationCode
            ]);

        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur transport email', [
                'error' => $e->getMessage(),
                'user_id' => $user->getId()
            ]);
            throw new \RuntimeException('Impossible d\'envoyer l\'email de confirmation. Vérifiez votre configuration mailer.');
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur inattendue lors de l\'envoi du code', [
                'error' => $e->getMessage(),
                'user_id' => $user->getId()
            ]);
            throw $e;
        }
    }

    /**
     * Verify the confirmation code
     */
    public function verifyCode(UserInterface $user, string $code): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        $expiresAt = $user->getCodeExpiresAt();
        $expiresAtString = null;
        if ($expiresAt instanceof \DateTimeInterface) {
            $expiresAtString = $expiresAt->format('Y-m-d H:i:s');
        }
        
        $this->logger->info('Vérification du code', [
            'user_id' => $user->getId(),
            'code_soumis' => $code,
            'code_stocké' => $user->getConfirmationCode(),
            'expire_le' => $expiresAtString
        ]);

        // Check if code matches
        if ($user->getConfirmationCode() !== $code) {
            $this->logger->warning('Code incorrect', [
                'user_id' => $user->getId(),
                'attendu' => $user->getConfirmationCode(),
                'reçu' => $code
            ]);
            return false;
        }

        // Check if code has expired
        if ($expiresAt instanceof \DateTimeInterface && $expiresAt < new \DateTime()) {
            $this->logger->warning('Code expiré', [
                'user_id' => $user->getId(),
                'expire_le' => $expiresAtString
            ]);
            return false;
        }

        // Mark user as verified
        $user->setIsVerified(true);
        $user->setConfirmationCode(null);
        $user->setCodeExpiresAt(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('Compte vérifié avec succès', [
            'user_id' => $user->getId()
        ]);

        return true;
    }
}
