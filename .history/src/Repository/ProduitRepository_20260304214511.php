<?php

namespace App\Repository;

use App\Entity\Produit;
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
}

