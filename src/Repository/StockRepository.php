<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stock|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stock[]    findAll()
 * @method Stock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    /**
     * @return Stock[] Returns stocks by exploitation
     */
    public function findByExploitation(int $exploitationId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exploitation = :id')
            ->setParameter('id', $exploitationId)
            ->orderBy('s.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Stock[] Returns stocks below alert threshold
     */
    public function findAlertes(int $exploitationId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exploitation = :id')
            ->andWhere('s.quantite < s.seuilAlerte')
            ->setParameter('id', $exploitationId)
            ->getQuery()
            ->getResult();
    }
}
