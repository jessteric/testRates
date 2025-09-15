<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use CryptoPairs;

/** @extends ServiceEntityRepository<Rate> */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }


    /** @return Rate[] */
    public function findRange(CryptoPairs $pair, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.pair = :pair')
            ->andWhere('r.collectedAt BETWEEN :from AND :to')
            ->setParameters([
                'pair' => $pair,
                'from' => $from,
                'to' => $to,
            ])
            ->orderBy('r.collectedAt', 'ASC')
            ->getQuery()->getResult();
    }
}