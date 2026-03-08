<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Admin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admin[]    findAll()
 * @method Admin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Find admin by user
     */
    public function findByUser($user): ?Admin
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find active admins
     */
    public function findActiveAdmins(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.estActif = :estActif')
            ->setParameter('estActif', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find admins by niveau
     */
    public function findByNiveau(string $niveau): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.niveau = :niveau')
            ->setParameter('niveau', $niveau)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find super admins
     */
    public function findSuperAdmins(): array
    {
        return $this->findByNiveau('super_admin');
    }

    /**
     * Count total admins
     */
    public function countAdmins(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count active admins
     */
    public function countActiveAdmins(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.estActif = :estActif')
            ->setParameter('estActif', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
