<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ProduitRepository $produitRepository, CategorieRepository $categorieRepository): Response
    {
        // Produits en vedette (les 8 derniers avec images)
        $produits = $produitRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            8
        );
        
        // Toutes les catégories
        $categories = $categorieRepository->findAll();

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
        ]);
    }
}
