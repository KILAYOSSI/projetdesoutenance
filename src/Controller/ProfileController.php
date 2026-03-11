<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Rafraîchir l'utilisateur depuis la base de données
        $user = $entityManager->getRepository(User::class)->find($user->getId());

        // Handle form submission
        if ($request->isMethod('POST')) {
            $nomComplet = $request->request->get('nomComplet');
            $telephone = $request->request->get('telephone');
            
            $user->setNomComplet($nomComplet);
            $user->setTelephone($telephone);
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Profil mis à jour avec succès !');
            
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}

