<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/produits/search", name="produits_search")
     */
    public function search(Request $request, ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        $search = $request->query->get('q', '');
        $categorieId = $request->query->get('categorie');
        
        $categories = $categorieRepository->findAll();
        $featuredProducts = $produitRepository->findFeatured(8);
        
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
            'featuredProducts' => $featuredProducts,
            'categoriesWithProducts' => $categoriesWithProducts,
        ]);
    }

    /**
     * @Route("/produits/categorie/{id}", name="produits_categorie", requirements={"id"="\d+"})
     */
    public function byCategory(int $id, ProduitRepository $produitRepository, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        $categories = $categorieRepository->findAll();
        $produits = $produitRepository->findBy(['categorie' => $categorie]);
        $featuredProducts = $produitRepository->findFeatured(8);
        
        $categoriesWithProducts = [];
        foreach ($categories as $cat) {
            $produitsCat = $produitRepository->findByCategory($cat);
            if (!empty($produitsCat)) {
                $categoriesWithProducts[] = [
                    'categorie' => $cat,
                    'produits' => $produitsCat,
                    'total' => count($produitsCat)
                ];
            }
        }
        
        return $this->render('product/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'categorie_active' => $categorie,
            'featuredProducts' => $featuredProducts,
            'categoriesWithProducts' => $categoriesWithProducts,
        ]);
    }

    /**
     * @Route("/produits/{id}", name="produits_detail", requirements={"id"="\d+"})
     */
    public function detail(int $id, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
        $similarProducts = [];
        if ($produit->getCategorie()) {
            $similarProducts = $produitRepository->findByCategory($produit->getCategorie());
            $similarProducts = array_filter($similarProducts, function($p) use ($id) {
                return $p->getId() !== $id;
            });
            $similarProducts = array_slice($similarProducts, 0, 4);
        }
        
        return $this->render('product/detail.html.twig', [
            'produit' => $produit,
            'similarProducts' => $similarProducts,
        ]);
    }
}
