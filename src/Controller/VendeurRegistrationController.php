<?php

namespace App\Controller;

use App\Entity\Kyc;
use App\Form\KycType;
use App\Form\VendeurStep1Type;
use App\Form\VendeurStep2Type;
use App\Form\VendeurStep3Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class VendeurRegistrationController extends AbstractController
{
    #[Route('/devenir-vendeur', name: 'devenir_vendeur')]
    public function index(): Response
    {
        return $this->render('vendeur_registration/index.html.twig');
    }

    // ========== ÉTAPE 1: Expérience ==========
    #[Route('/devenir-vendeur/etape-1', name: 'devenir_vendeur_step1')]
    public function step1(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(VendeurStep1Type::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('vendeur_step1', $form->getData());
            return $this->redirectToRoute('devenir_vendeur_step2');
        }

        return $this->render('vendeur_registration/step1.html.twig', [
            'form' => $form->createView(),
            'step' => 1,
            'total' => 4,
        ]);
    }

    // ========== ÉTAPE 2: Boutique ==========
    #[Route('/devenir-vendeur/etape-2', name: 'devenir_vendeur_step2')]
    public function step2(Request $request, SessionInterface $session): Response
    {
        if (!$session->has('vendeur_step1')) {
            return $this->redirectToRoute('devenir_vendeur_step1');
        }

        $form = $this->createForm(VendeurStep2Type::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('vendeur_step2', $form->getData());
            return $this->redirectToRoute('devenir_vendeur_step3');
        }

        return $this->render('vendeur_registration/step2.html.twig', [
            'form' => $form->createView(),
            'step' => 2,
            'total' => 4,
        ]);
    }

    // ========== ÉTAPE 3: Localisation ==========
    #[Route('/devenir-vendeur/etape-3', name: 'devenir_vendeur_step3')]
    public function step3(Request $request, SessionInterface $session): Response
    {
        if (!$session->has('vendeur_step1') || !$session->has('vendeur_step2')) {
            return $this->redirectToRoute('devenir_vendeur_step1');
        }

        $form = $this->createForm(VendeurStep3Type::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('vendeur_step3', $form->getData());
            return $this->redirectToRoute('devenir_vendeur_step4');
        }

        return $this->render('vendeur_registration/step3.html.twig', [
            'form' => $form->createView(),
            'step' => 3,
            'total' => 4,
        ]);
    }

    // ========== ÉTAPE 4: KYC ==========
    #[Route('/devenir-vendeur/etape-4', name: 'devenir_vendeur_step4')]
    public function step4(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        if (!$session->has('vendeur_step1') || !$session->has('vendeur_step2') || !$session->has('vendeur_step3')) {
            return $this->redirectToRoute('devenir_vendeur_step1');
        }

        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $kyc = new Kyc();
        $kyc->setUtilisateur($user);
        
        $form = $this->createForm(KycType::class, $kyc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file uploads
            $photoRecto = $form->get('photoPieceRecto')->getData();
            $photoVerso = $form->get('photoPieceVerso')->getData();
            $photoSelfie = $form->get('photoSelfie')->getData();
            
            $uploadDir = $this->getParameter('uploads_kyc_dir');
            
            if ($photoRecto) {
                $fileName = 'kyc_' . $user->getId() . '_recto_' . uniqid() . '.' . $photoRecto->guessExtension();
                $photoRecto->move($uploadDir, $fileName);
                $kyc->setPhotoPieceRecto('uploads/kyc/' . $fileName);
            }
            
            if ($photoVerso) {
                $fileName = 'kyc_' . $user->getId() . '_verso_' . uniqid() . '.' . $photoVerso->guessExtension();
                $photoVerso->move($uploadDir, $fileName);
                $kyc->setPhotoPieceVerso('uploads/kyc/' . $fileName);
            }
            
            if ($photoSelfie) {
                $fileName = 'kyc_' . $user->getId() . '_selfie_' . uniqid() . '.' . $photoSelfie->guessExtension();
                $photoSelfie->move($uploadDir, $fileName);
                $kyc->setPhotoSelfie('uploads/kyc/' . $fileName);
            }
            
            $kyc->setStatus(Kyc::STATUS_PENDING);
            $entityManager->persist($kyc);
            $entityManager->flush();
            
            // Clear session
            $session->remove('vendeur_step1');
            $session->remove('vendeur_step2');
            $session->remove('vendeur_step3');
            
            return $this->redirectToRoute('devenir_vendeur_succes');
        }

        return $this->render('vendeur_registration/step4.html.twig', [
            'form' => $form->createView(),
            'step' => 4,
            'total' => 4,
        ]);
    }

    // ========== SUCCÈS ==========
    #[Route('/devenir-vendeur/succes', name: 'devenir_vendeur_succes')]
    public function succes(): Response
    {
        return $this->render('vendeur_registration/succes.html.twig');
    }
}

