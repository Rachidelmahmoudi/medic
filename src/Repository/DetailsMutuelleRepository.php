<?php

namespace App\Repository;

use App\Entity\DetailsMutuelle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DetailsMutuelle|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsMutuelle|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsMutuelle[]    findAll()
 * @method DetailsMutuelle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsMutuelleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsMutuelle::class);
    }

    // /**
    //  * @return DetailsMutuelle[] Returns an array of DetailsMutuelle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DetailsMutuelle
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
