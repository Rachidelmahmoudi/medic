<?php

namespace App\Repository;

use App\Entity\TypeExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeExamen[]    findAll()
 * @method TypeExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeExamen::class);
    }

    // /**
    //  * @return TypeExamen[] Returns an array of TypeExamen objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeExamen
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
