<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Paiement;
use App\Entity\Notification;
use App\Service\FedapayService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;

#[Route('/paiement')]
class PaiementController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FedapayService $fedapayService;

    public function __construct(EntityManagerInterface $entityManager, FedapayService $fedapayService)
    {
        $this->entityManager = $entityManager;
        $this->fedapayService = $fedapayService;
    }

    /**
     * Page de paiement - Formulaire pour saisir le téléphone
     */
    #[Route('/initier/{commandeId}', name: 'paiement_initier', requirements: ['commandeId' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function initier(int $commandeId, Request $request): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($commandeId);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }
        
        // Vérifier que la commande appartient à l'utilisateur
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        // Vérifier si déjà payé
        if ($commande->getStatus() === Commande::STATUS_CONFIRMED) {
            $this->addFlash('info', 'Cette commande a déjà été payée.');
            return $this->redirectToRoute('commande_confirmee', ['id' => $commande->getId()]);
        }
        
        return $this->render('paiement/initier.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * Traite le paiement Fedapay
     */
    #[Route('/traiter/{commandeId}', name: 'paiement_traiter', requirements: ['commandeId' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function traiter(int $commandeId, Request $request): Response
    {
        $commande = $this->entityManager->getRepository(Commande::class)->find($commandeId);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }
        
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        $telephone = $request->request->get('telephone', '');
        
        if (empty($telephone)) {
            $this->addFlash('error', 'Veuillez entrer votre numéro de téléphone.');
            return $this->redirectToRoute('paiement_initier', ['commandeId' => $commandeId]);
        }
        
        // Formater le numéro de téléphone
        $telephone = $this->formatTelephone($telephone);
        
        // Créer la transaction Fedapay
        $result = $this->fedapayService->createPayment($commande, $telephone);
        
        if ($result['success']) {
            // Stocker l'ID du paiement en session
            $request->getSession()->set('paiement_en_cours', [
                'paiement_id' => $result['paiement_id'],
                'commande_id' => $commande->getId()
            ]);
            
            // Rediriger vers la page de paiement Fedapay
            return $this->redirect($result['url']);
        } else {
            $this->addFlash('error', 'Erreur lors du paiement: ' . $result['error']);
            return $this->redirectToRoute('paiement_initier', ['commandeId' => $commandeId]);
        }
    }

    /**
     * Retour après paiement (depuis Fedapay)
     */
    #[Route('/retour/{id}', name: 'paiement_retour', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function retour(int $id, Request $request): Response
    {
        $paiement = $this->entityManager->getRepository(Paiement::class)->find($id);
        
        if (!$paiement) {
            throw $this->createNotFoundException('Paiement non trouvé');
        }
        
        // Vérifier le statut du paiement
        $result = $this->fedapayService->checkPaymentStatus($id);
        
        if ($result['success'] && $paiement->getStatut() === 'succes') {
            // Paiement réussi - confirmer la commande
            $commande = $paiement->getCommande();
            $commande->setStatus(Commande::STATUS_CONFIRMED);
            
            // Notifier les vendeurs
            $this->notifierVendeurs($commande);
            
            // Vider le panier
            $session = $request->getSession();
            $session->remove('panier');
            
            $this->entityManager->flush();
            
            // Vider le panier
            $session = $request->getSession();
            $session->remove('panier');
            
            $this->addFlash('success', 'Paiement réussi ! Votre commande a été confirmée.');
            return $this->redirectToRoute('commande_confirmee', ['id' => $commande->getId()]);
        } else {
            // Paiement échoué ou en attente
            $this->addFlash('error', 'Le paiement n\'a pas été effectué ou a échoué.');
            return $this->redirectToRoute('paiement_initier', ['commandeId' => $paiement->getCommande()->getId()]);
        }
    }

    /**
     * Callback Fedapay ( webhook )
     */
    #[Route('/callback/{id}', name: 'paiement_callback', requirements: ['id' => '\d+'])]
    public function callback(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if ($data) {
            $this->fedapayService->handleCallback($data);
        }
        
        return new Response('OK');
    }

    /**
     * Vérifier le statut du paiement
     */
    #[Route('/statut/{id}', name: 'paiement_statut', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function statut(int $id): Response
    {
        $paiement = $this->entityManager->getRepository(Paiement::class)->find($id);
        
        if (!$paiement) {
            throw $this->createNotFoundException('Paiement non trouvé');
        }
        
        if ($paiement->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        
        // Vérifier le statut auprès de Fedapay
        $result = $this->fedapayService->checkPaymentStatus($id);
        
        return $this->json([
            'statut' => $paiement->getStatut(),
            'success' => $result['success']
        ]);
    }

    /**
     * Formater le numéro de téléphone
     */
    private function formatTelephone(string $telephone): string
    {
        // Enlever tous les espaces et caractères spéciaux
        $telephone = preg_replace('/[\s\-\.\(\)]/', '', $telephone);
        
        // Si le numéro commence par 0, le remplacer par +229
        if (str_starts_with($telephone, '0')) {
            $telephone = '+229' . substr($telephone, 1);
        }
        
        // Si le numéro ne commence pas par +, ajouter +229
        if (!str_starts_with($telephone, '+')) {
            $telephone = '+229' . $telephone;
        }
        
        return $telephone;
    }
}

