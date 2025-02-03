<?php

namespace App\Repository;

use App\Entity\SiteMessage;
use App\Enum\SiteMessagePlaceEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SiteMessage>
 */
class SiteMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteMessage::class);
    }

    public function findByPlace(SiteMessagePlaceEnum $place): ?SiteMessage
    {
        return $this->createQueryBuilder('site_message') // @phpstan-ignore return.type
            ->andWhere('site_message.place = :place')
            ->setParameter('place', $place->value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
