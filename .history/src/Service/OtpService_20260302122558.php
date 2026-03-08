<?php

namespace App\Service;

use App\Entity\OtpCode;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class OtpService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $entityManager, 
        MailerInterface $mailer, 
        Environment $twig,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    /**
     * Generate a 6-digit OTP code, save it to the database, and send it by email
     */
    public function generateAndSendOtp(User $user): string
    {
        // Generate a 6-digit random code
        $code = sprintf('%06d', random_int(0, 999999));
        
        $this->logger->info('Generating OTP code for user: ' . $user->getEmail());

        // Create new OtpCode entity
        $otpCode = new OtpCode();
        $otpCode->setCode($code);
        $otpCode->setUser($user);
        $otpCode->setCreatedAt(new \DateTime());
        $otpCode->setExpiresAt(new \DateTime('+10 minutes')); // Expires in 10 minutes
        $otpCode->setIsUsed(false);

        // Save to database - both OtpCode and User confirmation code
        $user->setConfirmationCode($code);
        $user->setCodeExpiresAt(new \DateTime('+10 minutes'));
        
        $this->entityManager->persist($otpCode);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info('OTP code saved to database. Code: ' . $code);

        // Send email with the code using Twig template
        $this->sendOtpEmail($user, $code);

        return $code;
    }

    /**
     * Send OTP code via email using Twig template
     */
    private function sendOtpEmail(User $user, string $code): void
    {
        try {
            $this->logger->info('Preparing to send email to: ' . $user->getEmail() . ' with code: ' . $code);
            
            // Log the code being sent
            $this->logger->info('CODE TO SEND: ' . $code);
            
            // Render the Twig template with the confirmation code
            $emailContent = $this->twig->render('registration/confirmation_email.html.twig', [
                'confirmationCode' => $code,
                'user' => $user,
            ]);

            $this->logger->debug('Email template rendered. Content: ' . $emailContent);

            $email = (new Email())
                ->from('constantkilayossi@gmail.com')
                ->to($user->getEmail())
                ->subject('Votre code de confirmation KilysAgri - Code: ' . $code)
                ->html($emailContent);

            $this->mailer->send($email);
            
            $this->logger->info('Email sent successfully to: ' . $user->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('Email sending failed: ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
        }
    }

    /**
     * Verify the OTP code
     */
    public function verifyCode(User $user, string $code): bool
    {
        // Find the most recent unused OTP code for this user
        $otpCode = $this->entityManager->getRepository(OtpCode::class)->findOneBy(
            [
                'user' => $user,
                'code' => $code,
                'isUsed' => false
            ],
            ['createdAt' => 'DESC']
        );

        if (!$otpCode) {
            return false;
        }

        // Check if code has expired
        if ($otpCode->isExpired()) {
            return false;
        }

        // Mark code as used
        $otpCode->setIsUsed(true);
        $this->entityManager->persist($otpCode);

        // Activate user account
        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}
