<?php

namespace App\Repository;

use App\Entity\LessonComponentInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LessonComponentInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonComponentInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonComponentInstance[]    findAll()
 * @method LessonComponentInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonComponentInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonComponentInstance::class);
    }

    // /**
    //  * @return LessonComponentInstance[] Returns an array of LessonComponentInstance objects
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
    public function findOneBySomeField($value): ?LessonComponentInstance
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param LessonComponentInstance $currentComponent
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNextLessonComponent(LessonComponentInstance $currentComponent) {

        $lesson = $currentComponent->getLesson();
        $nextInSequence = $currentComponent->getSequence() + 1;

        return $this->createQueryBuilder('lci')
                ->andWhere("lci.sequence = :nextInSequence")
                ->andWhere("lci.lesson = :lesson")
                ->setParameter("nextInSequence", $nextInSequence)
                ->setParameter("lesson", $lesson)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
