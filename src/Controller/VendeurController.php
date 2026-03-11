<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\ConversationRepository;
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

    #[Route('/dashboard', name: 'vendeur_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(ConversationRepository $conversationRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell()) {
            if ($user->isVendeur()) {
                return $this->redirectToRoute('kyc_status');
            }
            return $this->redirectToRoute('kyc_submit');
        }

        $produits = $this->entityManager->getRepository(Produit::class)->findBy(['utilisateur' => $user]);
        
        $revenus = 0;
        $commandesList = [];
        
        foreach ($produits as $produit) {
            $lignes = $this->entityManager->getRepository(LigneCommande::class)->findBy(['produit' => $produit]);
            foreach ($lignes as $ligne) {
                $revenus += $ligne->getQuantite() * $ligne->getPrixUnitaire();
                $commande = $ligne->getCommande();
                if (!isset($commandesList[$commande->getId()])) {
                    $commandesList[$commande->getId()] = $commande;
                }
            }
        }

        $commandes = array_values($commandesList);
        
        usort($commandes, function($a, $b) {
            return $b->getDateCommande() <=> $a->getDateCommande();
        });

        // Get conversations for the user
        $conversations = $conversationRepository->findByUser($user);
        
        // Calculate unread messages
        $unreadMessages = 0;
        foreach ($conversations as $conversation) {
            $unreadMessages += $conversation->getUnreadCountForUser($user);
        }

        return $this->render('vendeur/dashboard.html.twig', [
            'produits' => $produits,
            'revenus' => $revenus,
            'commandes' => $commandes,
            'commandesCount' => count($commandes),
            'produitsCount' => count($produits),
            'conversations' => $conversations,
            'unreadMessages' => $unreadMessages,
        ]);
    }

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

    #[Route('/produits/{id}/edit', name: 'vendeur_produit_edit')]
    #[IsGranted('ROLE_USER')]
    public function editProduit(Request $request, int $id): Response
    {
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

        $produits = $this->entityManager->getRepository(Produit::class)->findBy(['utilisateur' => $user]);
        $produitIds = array_map(fn($p) => $p->getId(), $produits);

        $lignes = $this->entityManager->getRepository(LigneCommande::class)->findBy(['produit' => $produitIds]);

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

    #[Route('/commandes/{id}/status', name: 'vendeur_commande_status')]
    #[IsGranted('ROLE_USER')]
    public function updateCommandeStatus(Request $request, int $id): Response
    {
        $user = $this->getUser();
        
        if (!$user->canSell()) {
            throw $this->createAccessDeniedException();
        }

        $commande = $this->entityManager->getRepository(Commande::class)->find($id);
        
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $status = $request->request->get('status');
        if (in_array($status, [Commande::STATUS_PENDING, Commande::STATUS_PROCESSING, Commande::STATUS_DELIVERED, Commande::STATUS_CANCELLED])) {
            $commande->setStatus($status);
            $this->entityManager->flush();
            $this->addFlash('success', 'Statut de la commande mis à jour !');
        }

        return $this->redirectToRoute('vendeur_commandes');
    }

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
