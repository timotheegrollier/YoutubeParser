<?php

namespace App\Repository;

use App\Entity\Youtube;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Youtube|null find($id, $lockMode = null, $lockVersion = null)
 * @method Youtube|null findOneBy(array $criteria, array $orderBy = null)
 * @method Youtube[]    findAll()
 * @method Youtube[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YoutubeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Youtube::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('y')
            ->select('COUNT(y.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
