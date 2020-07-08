<?php

namespace App\Repository;

use App\Entity\Consultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Consultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consultation[]    findAll()
 * @method Consultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    // /**
    //  * @return Consultation[] Returns an array of Consultation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Consultation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findTodayOrBetween($d1, $d2)
    {
        $qb = $this->createQueryBuilder('c');
        if ($d1==null && $d2==null) {
            return  $qb->select('distinct c')
                ->leftJoin('App\Entity\ConsultationExamen', 'ce', 'with', 'ce.Consultation = c.id')
                ->where(
                    $qb->expr()->eq("DATE_FORMAT(c.date_consultation,'%Y-%m-%d')", "CURRENT_DATE()")
                )
                ->getQuery()
                ->getResult();
        } else {
            return $qb->select('distinct c')
                ->where('c.date_consultation >= :d1 ')
                ->andWhere('c.date_consultation <= :d2')
                ->setParameter('d1', date('Y-m-d', strtotime($d1)))
                ->setParameter('d2', date('Y-m-d', strtotime($d2)))
                ->getQuery()
                ->getResult();
        }
    }
}
