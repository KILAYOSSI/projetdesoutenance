<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(
        EmailVerifier $emailVerifier, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/api/verify-otp", name="app_verify_otp", methods={"POST"})
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? null;
        $code = $data['code'] ?? null;

        if (!$email || !$code) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email et code sont requis'
            ], 400);
        }

        // Find user by email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        // Verify the code using EmailVerifier
        $isValid = $this->emailVerifier->verifyCode($user, $code);

        if ($isValid) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Compte vérifié avec succès!'
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Code invalide ou expiré'
            ], 400);
        }
    }

    /**
     * @Route("/api/resend-otp", name="app_resend_otp", methods={"POST"})
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Email requis'
            ], 400);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }

        // Generate and send new confirmation code using EmailVerifier
        $emailObj = (new TemplatedEmail())
            ->from('constantkilayossi@gmail.com')
            ->to($user->getEmail())
            ->subject('Votre code de confirmation KilysAgri')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->emailVerifier->sendEmailConfirmation('app_verify_code', $user, $emailObj);

        return new JsonResponse([
            'success' => true,
            'message' => 'Nouveau code envoyé!'
        ]);
    }
}
