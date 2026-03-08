<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/vendeur')]
#[IsGranted('ROLE_USER')]
class VendeurController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * Dashboard principal du vendeur
     */
    #[Route('/dashboard', name: 'vendeur_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur peut vendre
        if (!$user->canSell()) {
            if ($user->isVendeur()) {
                return $this->redirectToRoute('kyc_status');
            }
            return $this->redirectToRoute('kyc_submit');
        }

        // Statistiques
        $produits = $this->entityManager->getRepository(Produit::class)->findBy(['utilisateur' => $user]);
        
        // Calculer les revenus et récupérer les commandes
        $revenus = 0;
        $commandesList = [];
        
        foreach ($produits as $produit) {
            $lignes = $this->entityManager->getRepository(LigneCommande::class)->findBy(['produit' => $produit]);
            foreach ($lignes as $ligne) {
                $revenus += $ligne->getQuantite() * $ligne->getPrixUnitaire();
                $commande = $ligne->getCommande();
                // Regrouper les lignes par commande
                if (!isset($commandesList[$commande->getId()])) {
                    $commandesList[$commande->getId()] = $commande;
                }
            }
        }

        // Convertir en tableau pour le tri
        $commandes = array_values($commandesList);
        
        // Trier par date (plus récentes en premier)
        usort($commandes, function($a, $b) {
            return $b->getDateCommande() <=> $a->getDateCommande();
        });

        return $this->render('vendeur/dashboard.html.twig', [
            'produits' => $produits,
            'revenus' => $revenus,
            'commandes' => $commandes,
            'commandesCount' => count($commandes),
            'produitsCount' => count($produits),
        ]);
    }

    /**
     * Liste des produits du vendeur
     */
    #[Route('/produits', name: 'vendeur_produits')]
    #[IsGranted('ROLE_USER')]
    public function produits(): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell()) {
            if ($user->isVendeur()) {
                return $this->redirectToRoute('kyc_status');
            }
            return $this->redirectToRoute('kyc_submit');
        }

        $produits = $this->entityManager->getRepository(Produit::class)->findBy(
            ['utilisateur' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('vendeur/produits.html.twig', [
            'produits' => $produits,
        ]);
    }

    /**
     * Ajouter un nouveau produit
     */
    #[Route('/produits/new', name: 'vendeur_produit_new')]
    #[IsGranted('ROLE_USER')]
    public function newProduit(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell()) {
            if ($user->isVendeur()) {
                return $this->redirectToRoute('kyc_status');
            }
            return $this->redirectToRoute('kyc_submit');
        }

        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $produit->setImage($this->uploadFile($imageFile, 'produits'));
            }

            $produit->setUtilisateur($user);
            $produit->setCreatedAt(new \DateTime());
            
            $this->entityManager->persist($produit);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('vendeur_produits');
        }

        return $this->render('vendeur/produit_form.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    /**
     * Modifier un produit
     */
    #[Route('/produits/{id}/edit', name: 'vendeur_produit_edit')]
    #[IsGranted('ROLE_USER')]
    public function editProduit(Request $request, int $id): Response
    {
        // Récupérer le produit manuellement depuis la base de données
    $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
         throw $this->createNotFoundException('Produit non trouvé');
        }

        $user = $this->getUser();
        
        if (!$user->canSell() || $produit->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce produit');
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $produit->setImage($this->uploadFile($imageFile, 'produits'));
            }

            $produit->setUpdatedAt(new \DateTime());
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('vendeur_produits');
        }

        return $this->render('vendeur/produit_form.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    /**
     * Supprimer un produit
     */
    #[Route('/produits/{id}/delete', name: 'vendeur_produit_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteProduit(Produit $produit): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell() || $produit->getUtilisateur() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce produit');
        }

        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès !');
        return $this->redirectToRoute('vendeur_produits');
    }

    /**
     * Voir les commandes de mes produits
     */
    #[Route('/commandes', name: 'vendeur_commandes')]
    #[IsGranted('ROLE_USER')]
    public function commandes(): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell()) {
            if ($user->isVendeur()) {
                return $this->redirectToRoute('kyc_status');
            }
            return $this->redirectToRoute('kyc_submit');
        }

        // Récupérer tous les produits du vendeur
        $produits = $this->entityManager->getRepository(Produit::class)->findBy(['utilisateur' => $user]);
        $produitIds = array_map(fn($p) => $p->getId(), $produits);

        // Récupérer les lignes de commande liées à ces produits
        $lignes = $this->entityManager->getRepository(LigneCommande::class)->findBy(['produit' => $produitIds]);

        // Regrouper par commande
        $commandes = [];
        foreach ($lignes as $ligne) {
            $commande = $ligne->getCommande();
            if (!isset($commandes[$commande->getId()])) {
                $commandes[$commande->getId()] = [
                    'commande' => $commande,
                    'lignes' => [],
                    'total' => 0
                ];
            }
            $commandes[$commande->getId()]['lignes'][] = $ligne;
            $commandes[$commande->getId()]['total'] += $ligne->getQuantite() * $ligne->getPrixUnitaire();
        }

        return $this->render('vendeur/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    #[Route('/commandes/{id}/status', name: 'vendeur_commande_status')]
    #[IsGranted('ROLE_USER')]
    public function updateCommandeStatus(Request $request, Commande $commande): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell()) {
            throw $this->createAccessDeniedException();
        }

        $status = $request->request->get('status');
        if (in_array($status, [Commande::STATUS_PENDING, Commande::STATUS_PROCESSING, Commande::STATUS_DELIVERED, Commande::STATUS_CANCELLED])) {
            $commande->setStatus($status);
            $this->entityManager->flush();
            $this->addFlash('success', 'Statut de la commande mis à jour !');
        }

        return $this->redirectToRoute('vendeur_commandes');
    }

    /**
     * Upload d'un fichier
     */
    private function uploadFile(UploadedFile $file, string $directory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('kernel.project_dir') . '/public/uploads/' . $directory,
            $newFilename
        );

        return '/uploads/' . $directory . '/' . $newFilename;
    }
}

