<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Passer une commande (checkout)
     */
    #[Route('/passer', name: 'commande_passer')]
    #[IsGranted('ROLE_USER')]
    public function passer(Request $request, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('produits_index');
        }

        // Créer la commande
        $commande = new Commande();
        $commande->setUtilisateur($user);
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus(Commande::STATUS_PENDING);

        $total = 0;

        // Créer les lignes de commande
        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(\App\Entity\Produit::class)->find($id);
            
            if (!$produit) {
                continue;
            }

            // Vérifier la disponibilité
            if ($produit->getQuantite() < $quantite) {
                $this->addFlash('error', 'Le produit "' . $produit->getNom() . '" n\'a plus assez de stock.');
                return $this->redirectToRoute('panier_index');
            }

            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setProduit($produit);
            $ligneCommande->setQuantite($quantite);
            $ligneCommande->setPrixUnitaire($produit->getPrix());

            $this->entityManager->persist($ligneCommande);

            // Réduire le stock
            $produit->setQuantite($produit->getQuantite() - $quantite);
            $this->entityManager->persist($produit);

            $total += $quantite * $produit->getPrix();
        }

        $commande->setMontantTotal($total);
        $this->entityManager->persist($commande);

        // Créer une notification pour le client
        $notification = new Notification();
        $notification->setUtilisateur($user);
        $notification->setTitre('Commande passée');
        $notification->setMessage('Votre commande #' . $commande->getId() . ' a été passée avec succès. Montant total: ' . number_format($total, 0, ',', ' ') . ' FCA');
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTime());
        $this->entityManager->persist($notification);

        // Notifier les vendeurs
        $this->notifierVendeurs($commande);

        $this->entityManager->flush();

        // Vider le panier
        $session->remove('panier');

        $this->addFlash('success', 'Votre commande a été passée avec succès !');
        
        return $this->redirectToRoute('commande_confirmee', ['id' => $commande->getId()]);
    }

    /**
     * Page de confirmation de commande
     */
    #[Route('/confirmee/{id}', name: 'commande_confirmee')]
    #[IsGranted('ROLE_USER')]
    public function confirmee(Commande $commande): Response
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/confirmee.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * Liste des commandes de l'utilisateur
     */
    #[Route('/mes-commandes', name: 'commande_mes_commandes')]
    #[IsGranted('ROLE_USER')]
    public function mesCommandes(): Response
    {
        $user = $this->getUser();
        $commandes = $this->entityManager->getRepository(Commande::class)->findBy(
            ['utilisateur' => $user],
            ['dateCommande' => 'DESC']
        );

        return $this->render('commande/mes_commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * Détails d'une commande
     */
    #[Route('/details/{id}', name: 'commande_details')]
    #[IsGranted('ROLE_USER')]
    public function details(Commande $commande): Response
    {
        // Vérifier que la commande appartient à l'utilisateur
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('commande/details.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * Notifier les vendeurs d'une nouvelle commande
     */
    private function notifierVendeurs(Commande $commande): void
    {
        // Regrouper les produits par vendeur
        $vendeurs = [];
        foreach ($commande->getLigneCommandes() as $ligne) {
            $vendeur = $ligne->getProduit()->getUtilisateur();
            if ($vendeur && !isset($vendeurs[$vendeur->getId()])) {
                $vendeurs[$vendeur->getId()] = $vendeur;
            }
        }

        // Créer une notification pour chaque vendeur
        foreach ($vendeurs as $vendeur) {
            $notification = new Notification();
            $notification->setUtilisateur($vendeur);
            $notification->setTitre('Nouvelle commande');
            $notification->setMessage('Vous avez une nouvelle commande (#' . $commande->getId() . ')');
            $notification->setIsRead(false);
            $notification->setCreatedAt(new \DateTime());
            $this->entityManager->persist($notification);
        }
    }
}

