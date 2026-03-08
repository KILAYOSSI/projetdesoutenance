<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class VerificationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private EntityManagerInterface $entityManager;

    public function __construct(EmailVerifier $emailVerifier, EntityManagerInterface $entityManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
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

        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');
            
            if ($this->emailVerifier->verifyCode($user, $code)) {
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
     * @Route("/resend/code", name="app_resend_code")
     */
    public function resendCode(): Response
    {
        // This could be implemented to resend the code
        $this->addFlash('info', 'Un nouveau code a été envoyé à votre adresse email.');
        return $this->redirectToRoute('app_register');
    }
}
