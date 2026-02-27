<?php

namespace App\Repository;

use App\Entity\PortfolioValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PortfolioValue>
 */
class PortfolioValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortfolioValue::class);
    }

    /**
     * @return PortfolioValue[]
     */
    public function getPortfolioValues(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.calculatedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PortfolioValue[]
     */
    public function getPortfolioValuesByRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.calculatedAt', 'ASC')
            ->andWhere('p.calculatedAt >= :from')
            ->setParameter('from', $from)
            ->andWhere('p.calculatedAt <= :to')
            ->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    public function getPortfolioValuesFrom(\DateTimeImmutable $from): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.calculatedAt >= :from')
            ->setParameter('from', $from)
            ->orderBy('p.calculatedAt', 'ASC');

        return $qb->getQuery()
            ->getResult();
    }
}
