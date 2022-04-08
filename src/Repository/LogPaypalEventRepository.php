<?php

namespace App\Repository;

use App\Entity\LogPaypalEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogPaypalEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogPaypalEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogPaypalEvent[]    findAll()
 * @method LogPaypalEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogPaypalEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogPaypalEvent::class);
    }

    // /**
    //  * @return LogPaypalEvent[] Returns an array of LogPaypalEvent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogPaypalEvent
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
