<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * Search products by name
     * @return Produit[]
     */
    public function searchByName(string $searchTerm)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom LIKE :search OR p.description LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products with stock available
     * @return Produit[]
     */
    public function findWithStock()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.quantite > 0')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find featured/popular products (all products, ordered by creation date)
     * @return Produit[]
     */
    public function findFeatured(int $limit = 10)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by category with limit
     * @return Produit[]
     */
    public function findByCategory(Categorie $categorie, int $limit = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.categorie = :categorie')
            ->andWhere('p.quantite > 0')
            ->setParameter('categorie', $categorie)
            ->orderBy('p.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all categories with their product counts
     * @return array
     */
    public function getCategoriesWithProductCount()
    {
        return $this->createQueryBuilder('p')
            ->select('c.id, c.nom, COUNT(p.id) as productCount')
            ->join('p.categorie', 'c')
            ->groupBy('c.id', 'c.nom')
            ->orderBy('productCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

