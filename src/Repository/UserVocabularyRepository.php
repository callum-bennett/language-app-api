<?php

namespace App\Repository;

use App\Entity\UserVocabulary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserVocabulary|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserVocabulary|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserVocabulary[]    findAll()
 * @method UserVocabulary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserVocabularyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserVocabulary::class);
    }

    // /**
    //  * @return UserVocabulary[] Returns an array of UserVocabulary objects
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
    public function findOneBySomeField($value): ?UserVocabulary
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
