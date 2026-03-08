<?php

namespace App\Repository;

use App\Entity\Equipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipement[]    findAll()
 * @method Equipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipement::class);
    }

    /**
     * @return Equipement[] Returns equipements by exploitation
     */
    public function findByExploitation(int $exploitationId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exploitation = :id')
            ->setParameter('id', $exploitationId)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
