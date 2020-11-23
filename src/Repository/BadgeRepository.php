<?php

namespace App\Repository;

use App\Entity\Badge;
use App\Entity\User;
use App\Entity\UserBadge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Badge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badge[]    findAll()
 * @method Badge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeRepository extends ServiceEntityRepository
{
    public const WORD = "word";
    public const LESSON = "lesson";

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }

    /**
     * @param User $user
     * @param string $notifier
     * @return int|mixed|string
     */
    public function getUnobtainedBadgesByNotifier(User $user, string $notifier) {

        $subQb = $this->createQueryBuilder('sub')
                    ->from(UserBadge::class, 'ub')
                    ->where('ub.badge = b.id')
                    ->andWhere('ub.user = :user');

        $qb = $this->createQueryBuilder('b', 'b.shortname');

        return $qb
                ->where('b.notifier = :notifier')
                ->andWhere($qb->expr()->not($qb->expr()->exists($subQb->getDQL())))
                ->setParameter('notifier', $notifier)
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
    }

    /**
     * @param User $user
     * @param string $name
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUnobtainedBadgeForUser(User $user, string $name) {

        $subQb = $this->createQueryBuilder('sub')
                ->from(UserBadge::class, 'ub')
                ->where('ub.badge = b.id')
                ->andWhere('ub.user = :user');

        $qb = $this->createQueryBuilder('b');

        return $qb
                ->where('b.shortname = :name')
                ->andWhere($qb->expr()->not($qb->expr()->exists($subQb->getDQL())))
                ->setParameter('name', $name)
                ->setParameter('user', $user)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
