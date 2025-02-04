<?php

namespace App\Repository;

use App\Entity\Crossing;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return Review[]
     */
    public function findAllForCrossing(Crossing $crossing): array
    {
        return $this->createQueryBuilder('review') // @phpstan-ignore return.type
            ->andWhere('review.crossing = :crossing')
            ->setParameter('crossing', $crossing)
            ->addOrderBy('review.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
