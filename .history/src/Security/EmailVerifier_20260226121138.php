<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer, EntityManagerInterface $manager)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->entityManager = $manager;
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        // Generate a 6-digit confirmation code
        $confirmationCode = sprintf('%06d', random_int(0, 999999));
        
        // Set the code and expiration (15 minutes)
        $user->setConfirmationCode($confirmationCode);
        $user->setCodeExpiresAt(new \DateTime('+15 minutes'));
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Add the code to the email context
        $context = $email->getContext();
        $context['confirmationCode'] = $confirmationCode;

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * Verify the confirmation code
     */
    public function verifyCode(UserInterface $user, string $code): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        // Check if code matches and hasn't expired
        if ($user->getConfirmationCode() !== $code) {
            return false;
        }

        if ($user->getCodeExpiresAt() < new \DateTime()) {
            return false;
        }

        // Mark user as verified
        $user->setIsVerified(true);
        $user->setConfirmationCode(null);
        $user->setCodeExpiresAt(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
