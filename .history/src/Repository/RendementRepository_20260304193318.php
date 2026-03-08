<?php

namespace App\Repository;

use App\Entity\Rendement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rendement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rendement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rendement[]    findAll()
 * @method Rendement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rendement::class);
    }

    /**
     * @return Rendement[] Returns rendements by culture
     */
    public function findBySuiviCulture(int $suiviCultureId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.suiviCulture = :id')
            ->setParameter('id', $suiviCultureId)
            ->orderBy('r.dateRecolte', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
