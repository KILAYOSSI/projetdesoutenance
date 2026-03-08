<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Kyc;
use App\Entity\Categorie;
use App\Entity\Paiement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Annotation\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $totalUsers = $this->entityManager->getRepository(User::class)->count([]);
        $totalProducteurs = $this->entityManager->getRepository(User::class)->count(['isVendeur' => true]);
        $totalAcheteurs = $totalUsers - $totalProducteurs;

        $totalProduits = $this->entityManager->getRepository(Produit::class)->count([]);
        
        $totalCommandes = $this->entityManager->getRepository(Commande::class)->count([]);
        
        $commandesEnAttente = $this->entityManager->getRepository(Commande::class)->count(['status' => 'en_attente']);
        $commandesEnCours = $this->entityManager->getRepository(Commande::class)->count(['status' => 'en_cours']);
        $commandesLivrees = $this->entityManager->getRepository(Commande::class)->count(['status' => 'livre']);

        $totalPaiements = $this->entityManager->getRepository(Paiement::class)->count([]);
        
        $paiements = $this->entityManager->getRepository(Paiement::class)->findAll();
        $revenusTotaux = 0;
        foreach ($paiements as $paiement) {
            if ($paiement->getStatut() === 'valide') {
                $revenusTotaux += $paiement->getMontant();
            }
        }

        $kycsEnAttente = $this->entityManager->getRepository(Kyc::class)->count(['status' => 'en_attente']);

        $categories = $this->entityManager->getRepository(Categorie::class)->findAll();

        $dernieresCommandes = $this->entityManager->getRepository(Commande::class)->findBy(
            [],
            ['dateCommande' => 'DESC'],
            5
        );

        $derniersUtilisateurs = $this->entityManager->getRepository(User::class)->findBy(
            [],
            ['id' => 'DESC'],
            5
        );

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalProducteurs' => $totalProducteurs,
            'totalAcheteurs' => $totalAcheteurs,
            'totalProduits' => $totalProduits,
            'totalCommandes' => $totalCommandes,
            'commandesEnAttente' => $commandesEnAttente,
            'commandesEnCours' => $commandesEnCours,
            'commandesLivrees' => $commandesLivrees,
            'totalPaiements' => $totalPaiements,
            'revenusTotaux' => $revenusTotaux,
            'kycsEnAttente' => $kycsEnAttente,
            'categories' => $categories,
            'dernieresCommandes' => $dernieresCommandes,
            'derniersUtilisateurs' => $derniersUtilisateurs,
        ]);
    }

    #[Route('/utilisateurs', name: 'admin_users')]
    public function users(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/utilisateur/{id}/toggle-role', name: 'admin_user_toggle_role')]
    public function toggleUserRole(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('admin_users');
        }
        
        $roles = $user->getRoles();
        
        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_filter($roles, fn($r) => $r !== 'ROLE_ADMIN');
            $user->setRoles(array_values($roles));
            $this->addFlash('success', 'Rôle admin retiré à l\'utilisateur.');
        } else {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles(array_unique($roles));
            $this->addFlash('success', 'Rôle admin ajouté à l\'utilisateur.');
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/produits', name: 'admin_produits')]
    public function produits(): Response
    {
        $produits = $this->entityManager->getRepository(Produit::class)->findAll();

        return $this->render('admin/produits.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/{id}/delete', name: 'admin_produit_delete')]
    public function deleteProduit(Produit $produit): Response
    {
        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès.');

        return $this->redirectToRoute('admin_produits');
    }

    #[Route('/categories', name: 'admin_categories')]
    public function categories(): Response
    {
        $categories = $this->entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categorie/new', name: 'admin_categorie_new')]
    public function newCategorie(Request $request): Response
    {
        $nom = $request->request->get('nom');
        
        if ($nom) {
            $categorie = new Categorie();
            $categorie->setNom($nom);
            
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Catégorie ajoutée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categorie_form.html.twig', [
            'categorie' => null,
        ]);
    }

    #[Route('/categorie/{id}/delete', name: 'admin_categorie_delete')]
    public function deleteCategorie(Categorie $categorie): Response
    {
        $this->entityManager->remove($categorie);
        $this->entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');

        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/commandes', name: 'admin_commandes')]
    public function commandes(): Response
    {
        $commandes = $this->entityManager->getRepository(Commande::class)->findBy(
            [],
            ['dateCommande' => 'DESC']
        );

        return $this->render('admin/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/{id}', name: 'admin_commande_detail')]
    public function commandeDetail(Commande $commande): Response
    {
        return $this->render('admin/commande_detail.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/paiements', name: 'admin_paiements')]
    public function paiements(): Response
    {
        $paiements = $this->entityManager->getRepository(Paiement::class)->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('admin/paiements.html.twig', [
            'paiements' => $paiements,
        ]);
    }
}

