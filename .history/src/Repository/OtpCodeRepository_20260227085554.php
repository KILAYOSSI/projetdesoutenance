<?php

namespace App\Repository;

use App\Entity\OtpCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OtpCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method OtpCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method OtpCode[]    findAll()
 * @method OtpCode[]    findBy(array $criteria, array $orderBy = null, $limit = null)
 */
class OtpCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OtpCode::class);
    }

    // Add custom methods as needed
}
