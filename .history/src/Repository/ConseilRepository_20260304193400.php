<?php

namespace App\Repository;

use App\Entity\Conseil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conseil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conseil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conseil[]    findAll()
 * @method Conseil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConseilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conseil::class);
    }

    /**
     * @return Conseil[] Returns published conseils
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.estPublie = :true')
            ->setParameter('true', true)
            ->orderBy('c.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Conseil[] Returns conseils by type
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->andWhere('c.estPublie = :true')
            ->setParameter('type', $type)
            ->setParameter('true', true)
            ->orderBy('c.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
