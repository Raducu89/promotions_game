<?php

namespace App\Repository;

use App\Entity\Prizes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prizes>
 *
 * @method Prizes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prizes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prizes[]    findAll()
 * @method Prizes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrizesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prizes::class);
    }

    public function findAvailablePrize(string $language): ?Prizes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.is_available = :available')
            ->andWhere('p.language = :language')
            ->setParameter('available', 1)
            ->setParameter('language', $language)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRandomAvailablePrize(string $language): ?Prizes
    {
        $prizes = $this->createQueryBuilder('p')
            ->andWhere('p.is_available = :available')
            ->andWhere('p.language = :language')
            ->setParameter('available', 1)
            ->setParameter('language', $language)
            ->getQuery()
            ->getResult();

        if (count($prizes) === 0) {
            return null;
        }

        $randomIndex = array_rand($prizes);
        return $prizes[$randomIndex];
    }
    

    public function countAvailablePrizes(): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.is_available = :available')
            ->setParameter('available', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
