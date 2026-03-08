<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/produits", name="produits_index")
     */
    public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        $produits = $produitRepository->findAll();
        $featuredProducts = $produitRepository->findFeatured(8);
        
        // Get products grouped by category
        $categoriesWithProducts = [];
        foreach ($categories as $categorie) {
            $produitsCat = $produitRepository->findByCategory($categorie);
            if (!empty($produitsCat)) {
                $categoriesWithProducts[] = [
                    'categorie' => $categorie,
                    'produits' => $produitsCat,
                    'total' => count($produitsCat)
                ];
            }
        }
        
        return $this->render('product/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'categoriesWithProducts' => $categoriesWithProducts,
        ]);
    }

    /**
     * @Route("/produits/vedettes", name="produits_vedettes")
     */
    public function featured(ProduitRepository $produitRepository): Response
    {
        $featuredProducts = $produitRepository->findFeatured(12);
        
        return $this->render('product/featured.html.twig', [
            'featuredProducts' => $featuredProducts,
        ]);
    }


     */
    public function byCategory(Categorie $categorie, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        $produits = $produitRepository->findBy(['categorie' => $categorie]);
        
        return $this->render('product/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'categorie_active' => $categorie,
        ]);
    }

    /**
     * @Route("/produits/{id}", name="produits_detail", requirements={"id"="\d+"})
     */
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
        return $this->render('product/detail.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/produits/search", name="produits_search")
     */
    public function search(Request $request, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $search = $request->query->get('q', '');
        $categorieId = $request->query->get('categorie');
        
        $categories = $categorieRepository->findAll();
        
        if ($search) {
            $produits = $produitRepository->searchByName($search);
        } elseif ($categorieId) {
            $categorie = $categorieRepository->find($categorieId);
            $produits = $produitRepository->findBy(['categorie' => $categorie]);
        } else {
            $produits = $produitRepository->findAll();
        }
        
        return $this->render('product/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'search' => $search,
        ]);
    }
}
