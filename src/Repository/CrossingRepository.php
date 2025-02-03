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
            ->orderBy('crossing.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
