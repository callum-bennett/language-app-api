<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\XP;
use App\Repository\XPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class XPService
{
    public const DAILY = "daily";
    public const WEEKLY = "weekly";
    public const MONTHLY = "monthly";

    public const CORRECT_ANSWER_XP_POINTS = 1;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var XPRepository
     */
    private $repository;

    private function getAvailableTypes() {
        return [
                self::DAILY,
                self::WEEKLY,
                self::MONTHLY
        ];
    }

    /**
     * XPService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(XP::class);
    }

    /**
     * @param string $type
     * @throws Exception
     */
    public function clearXP(string $type) {

        if (!in_array($type, $this->getAvailableTypes())) {
            throw new Exception("Invalid XP frequency");
        }

        $table = XP::class;
        $query = $this->em->createQuery("update $table xp set xp.$type = 0");
        $query->execute();
    }

    /**
     * @param User $user
     * @param int $points
     * @return XP
     */
    public function updateXP(User $user, int $points) {

        if (!$xpRecord = $this->repository->findOneBy(["user" => $user])) {
            $xpRecord = new XP();
            $xpRecord->setUser($user);
        }

        $newDaily = $xpRecord->getDaily() + $points;
        $newWeekly = $xpRecord->getWeekly() + $points;
        $newMonthly = $xpRecord->getMonthly() + $points;

        $xpRecord->setDaily($newDaily);
        $xpRecord->setWeekly($newWeekly);
        $xpRecord->setMonthly($newMonthly);
        $this->em->persist($xpRecord);
        $this->em->flush();

        return $xpRecord;
    }
}
