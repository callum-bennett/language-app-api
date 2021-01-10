<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\XP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method XP|null find($id, $lockMode = null, $lockVersion = null)
 * @method XP|null findOneBy(array $criteria, array $orderBy = null)
 * @method XP[]    findAll()
 * @method XP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class XPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
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

    /**
     * @param $type
     * @param int $limit
     * @return int|mixed|string
     */
    public function getTopXUsersByType($type, $limit = 10)
    {
        return $this->createQueryBuilder('xp')
                ->select("xp.$type as score, u.username")
                ->join("xp.user", "u")
                ->setMaxResults($limit)
                ->andWhere("xp.$type > 0")
                ->orderBy("xp.$type", "DESC")
                ->getQuery()
                ->getResult();
    }

    /**
     * @param User $user
     * @param $type
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserScoreByType(User $user, $type)
    {
        return $this->createQueryBuilder('xp')
                ->select("xp.$type as score, u.username")
                ->join("xp.user", "u")
                ->andWhere('xp.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }
}
