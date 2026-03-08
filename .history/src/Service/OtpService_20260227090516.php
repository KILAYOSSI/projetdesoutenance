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
            ->html(sprintf('
