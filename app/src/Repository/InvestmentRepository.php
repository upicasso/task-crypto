<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Investment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Investment>
 */
class InvestmentRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Investment::class);
    }

    /**
     * @return string[]
     */
    public function findAllNames(): array
    {
        return $this->createQueryBuilder('i')
            ->select('i.name')
            ->getQuery()
            ->getSingleColumnResult();
    }

}
