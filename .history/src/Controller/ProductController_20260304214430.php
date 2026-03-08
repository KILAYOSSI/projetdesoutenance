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
        
        return $this->render('product/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/produits/categorie/{id}", name="produits_categorie")
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
     * @Route("/produits/{id}", name="produits_detail")
     */
    public function detail(Produit $produit): Response
    {
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
