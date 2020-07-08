<?php

namespace App\Repository;

use App\Entity\ConsultationExamen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConsultationExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConsultationExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConsultationExamen[]    findAll()
 * @method ConsultationExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultationExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsultationExamen::class);
    }

    // /**
    //  * @return ConsultationExamen[] Returns an array of ConsultationExamen objects
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
    public function findOneBySomeField($value): ?ConsultationExamen
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findTodayOrBetween($d1=null, $d2=null)
    {
        $qb = $this->createQueryBuilder('ce');
        $qb->innerJoin('App\Entity\Consultation', 'c', 'with', 'ce.Consultation = c.id');

        if ($d1==null && $d2==null) {
            return  $qb->select(array('ce'))
                ->where(
                    $qb->expr()->eq("DATE_FORMAT(c.date_consultation,'%Y-%m-%d')", "CURRENT_DATE()")
                )
                ->getQuery()
                ->getResult();
        } else {
            return $qb->select('distinct ce')
                ->where('c.date_consultation >= :d1 ')
                ->andWhere('c.date_consultation <= :d2')
                ->setParameter('d1', date('Y-m-d', strtotime($d1)))
                ->setParameter('d2', date('Y-m-d', strtotime($d2)))
                ->getQuery()
                ->getResult();
        }
    }

    public function findByConsultation($idconsult)
    {
        return $this->createQueryBuilder('ce')
            ->innerJoin('App\Entity\Examen','e','with','ce.examen = e.id')
            ->innerJoin('App\Entity\Consultation','c','with','c.id = ce.Consultation')
            ->select(array('ce'))
            ->Where('ce.Consultation = :conc')
            ->setParameter('conc', $idconsult)
            ->getQuery()
            ->getResult();
    }
    
    public  function  findFacturesnogeneres($idconsult)
    {
        $r1 = $this->createQueryBuilder('ce')
            ->leftJoin('App\Entity\FactureConsultationExamen','fce','with','fce.consult_examen = ce.id')
            ->select(array('ce'))
            ->Where('ce.Consultation = :conc')
            ->setParameter('conc', $idconsult)
            ->andWhere('ce.statut = 2 ')
            ->andWhere('fce is NULL')
            ->getQuery()
            ->getResult();

        return $r1;
    }

}
