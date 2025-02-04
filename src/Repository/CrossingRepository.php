<?php

namespace App\Repository;

use App\Entity\Crossing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Crossing>
 */
class CrossingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Crossing::class);
    }

    /**
     * @return Crossing[]
     */
    public function findLastCrossings(int $limit): array
    {
        return $this->createQueryBuilder('crossing') // @phpstan-ignore return.type
            ->orderBy('crossing.date', 'ASC')
            ->andWhere('crossing.date > :now')
            ->setParameter('now', new \DateTime())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Crossing[]
     */
    public function findAllFuturCrossings(): array
    {
        return $this->createQueryBuilder('crossing') // @phpstan-ignore return.type
            ->orderBy('crossing.date', 'ASC')
            ->andWhere('crossing.date > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
