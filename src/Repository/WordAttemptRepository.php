<?php

namespace App\Repository;

use App\Entity\WordAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WordAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method WordAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method WordAttempt[]    findAll()
 * @method WordAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordAttempt::class);
    }

    // /**
    //  * @return WordAttempt[] Returns an array of WordAttempt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WordAttempt
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
