<?php

namespace App\Repository;

use App\Entity\Examen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Examen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Examen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Examen[]    findAll()
 * @method Examen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Examen::class);
    }

    // /**
    //  * @return Examen[] Returns an array of Examen objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Examen
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByConsultation($consu)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('App\Entity\ConsultationExamen', 'ce', 'with', 'ce.examen = e.id')
            ->innerJoin('App\Entity\Consultation', 'c', 'with', 'ce.Consultation = c.id')
            ->select('e')
            ->Where('c.id = :conc')
            ->setParameter('conc', $consu)
            ->getQuery()
            ->getResult();
    }
}
