<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
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
     * @Route("/verify/code/{id}", name="app_verify_code")
     */
    public function verifyCode(Request $request, int $id): Response
    {
        // Get the user by ID
        $user = $this->entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_register');
        }

        // If user is already verified, redirect to login
        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre email est déjà vérifié. Veuillez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        // Check if code has expired
        if ($user->getCodeExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'Le code a expiré. Veuillez demander un nouveau code.');
            return $this->redirectToRoute('app_resend_code', ['id' => $user->getId()]);
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');
            
            // Verify the code using EmailVerifier
            $isValid = $this->emailVerifier->verifyCode($user, $code);
            
            if ($isValid) {
                $this->addFlash('success', 'Votre email a été vérifié avec succès! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Code invalide ou expiré. Veuillez réessayer.');
            }
        }

        return $this->render('registration/verify_code.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/resend/code/{id}", name="app_resend_code")
     */
    public function resendCode(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_register');
        }

        // If user is already verified, redirect to login
        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre email est déjà vérifié.');
            return $this->redirectToRoute('app_login');
        }

        // Generate and send new confirmation code using EmailVerifier
        $email = (new TemplatedEmail())
            ->from('constantkilayossi@gmail.com')
            ->to($user->getEmail())
            ->subject('Nouveau code de confirmation KilysAgri')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context([
                'user' => $user,
            ]);

        $this->emailVerifier->sendEmailConfirmation('app_verify_code', $user, $email);
        
        $this->addFlash('success', 'Un nouveau code a été envoyé à votre adresse email.');
        
        return $this->redirectToRoute('app_verify_code', ['id' => $user->getId()]);
    }
}
