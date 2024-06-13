<?php

namespace App\Repository;

use App\Entity\UserPrizes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserPrizesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPrizes::class);
    }

    public function countDistributedPrizes(\DateTime $date): int
    {
        return $this->createQueryBuilder('up')
            ->select('count(up.id)')
            ->andWhere('up.date_played = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
