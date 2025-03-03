<?php

namespace App\Repository;

use App\Entity\Port;
use App\Enum\CountryEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Port>
 */
class PortRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Port::class);
    }

    /**
     * @return Port[]
     */
    public function findByCountry(CountryEnum $country): array
    {
        return $this->createQueryBuilder('p') // @phpstan-ignore return.type
            ->andWhere('p.country = :country')
            ->setParameter('country', $country->value)
            ->getQuery()
            ->getResult();
    }
}
