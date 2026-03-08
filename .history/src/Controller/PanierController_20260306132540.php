<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;

#[Route('/panier')]
class PanierController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Voir le panier
     */
    #[Route('/', name: 'panier_index')]
    public function index(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $produits = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $produits[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'sousTotal' => $quantite * $produit->getPrix()
                ];
                $total += $quantite * $produit->getPrix();
            }
        }

        return $this->render('panier/index.html.twig', [
            'produits' => $produits,
            'total' => $total,
        ]);
    }


    {
        $quantite = $request->request->get('quantite', 1);
        
        if ($produit->getQuantite() < $quantite) {
            $this->addFlash('error', 'La quantité demandée n\'est pas disponible.');
            return $this->redirectToRoute('produits_detail', ['id' => $produit->getId()]);
        }

        $panier = $session->get('panier', []);
        $id = $produit->getId();

        if (isset($panier[$id])) {
            $panier[$id] += $quantite;
        } else {
            $panier[$id] = $quantite;
        }

        // Vérifier le stock
        if ($panier[$id] > $produit->getQuantite()) {
            $panier[$id] = $produit->getQuantite();
            $this->addFlash('warning', 'Quantité ajustée au stock disponible.');
        }

        $session->set('panier', $panier);

        $this->addFlash('success', $produit->getNom() . ' a été ajouté au panier.');
        
        // Rediriger vers la page précédente ou le panier
        $redirect = $request->headers->get('referer');
        if (!$redirect || strpos($redirect, '/panier') !== false) {
            return $this->redirectToRoute('produits_index');
        }
        return $this->redirect($redirect);
    }

    /**
     * Modifier la quantité dans le panier
     */
    #[Route('/modifier/{id}', name: 'panier_modifier')]
    public function modifier(Produit $produit, SessionInterface $session, Request $request): Response
    {
        $quantite = (int) $request->request->get('quantite', 1);
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        if ($quantite <= 0) {
            unset($panier[$id]);
        } else {
            // Vérifier le stock
            if ($quantite > $produit->getQuantite()) {
                $quantite = $produit->getQuantite();
                $this->addFlash('warning', 'Quantité ajustée au stock disponible.');
            }
            $panier[$id] = $quantite;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier_index');
    }

    /**
     * Supprimer un produit du panier
     */
    #[Route('/supprimer/{id}', name: 'panier_supprimer')]
    public function supprimer(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        if (isset($panier[$id])) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('success', $produit->getNom() . ' a été supprimé du panier.');
        }

        return $this->redirectToRoute('panier_index');
    }

    /**
     * Vider le panier
     */
    #[Route('/vider', name: 'panier_vider')]
    public function vider(SessionInterface $session): Response
    {
        $session->remove('panier');
        $this->addFlash('success', 'Le panier a été vidé.');

        return $this->redirectToRoute('panier_index');
    }

    /**
     * Obtenir le nombre d'articles dans le panier (API)
     */
    #[Route('/count', name: 'panier_count')]
    public function count(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $count = array_sum($panier);

        return $this->json(['count' => $count]);
    }
}

