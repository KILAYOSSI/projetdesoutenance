<?php

namespace App\Service;

use App\Entity\OtpCode;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class OtpService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * Generate a 6-digit OTP code, save it to the database, and send it by email
     */
    public function generateAndSendOtp(User $user): string
    {
        // Generate a 6-digit random code
        $code = sprintf('%06d', random_int(0, 999999));

        // Create new OtpCode entity
        $otpCode = new OtpCode();
        $otpCode->setCode($code);
        $otpCode->setUser($user);
        $otpCode->setCreatedAt(new \DateTime());
        $otpCode->setExpiresAt(new \DateTime('+10 minutes')); // Expires in 10 minutes
        $otpCode->setIsUsed(false);

        // Save to database
        $this->entityManager->persist($otpCode);
        $this->entityManager->flush();

        // Send email with the code
        $this->sendOtpEmail($user, $code);

        return $code;
    }

    /**
     * Send OTP code via email
     */
    private function sendOtpEmail(User $user, string $code): void
    {
        $email = (new Email())
            ->from('constantkilayossi@gmail.com')
            ->to($user->getEmail())
            ->subject('Votre code de confirmation KilysAgri')
            ->html(sprintf(
                '<h1>Bienvenue sur KilysAgri!</h1>
                <p>Votre code de confirmation est: <strong>%s</strong></p>
                <p>Ce code expire dans 10 minutes.</p>
                <p>Si vous n\'avez pas demandé ce code, vous pouvez ignorer cet email.</p>',
                $code
            ));

        $this->mailer->send($email);
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
