<?php

namespace App\Repository;

use App\Entity\XP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method XP|null find($id, $lockMode = null, $lockVersion = null)
 * @method XP|null findOneBy(array $criteria, array $orderBy = null)
 * @method XP[]    findAll()
 * @method XP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class XPRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, XP::class);
    }

    // /**
    //  * @return XP[] Returns an array of XP objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('x.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?XP
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
