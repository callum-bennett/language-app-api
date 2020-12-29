<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\XP;
use App\Repository\XPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Predis\Client;
use Symfony\Component\Serializer\SerializerInterface;

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

    private $redisClient;

    private $serializer;


    private function getAvailableTypes()
    {
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
     * @param Redis $redisClient
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, Client $redisClient, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(XP::class);
        $this->redisClient = $redisClient;
        $this->serializer = $serializer;
    }

    /**
     * @param string $type
     * @throws Exception
     */
    public function clearXP(string $type)
    {
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
    public function updateXP(User $user, int $points)
    {
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

    /**
     * @return bool
     */
    public function updateLeaderboard()
    {
        $toRedis = [];

        foreach ($this->getAvailableTypes() as $type) {
            $toRedis[$type] = $this->em->getRepository(XP::class)->getTopXUsersByType($type);
        }

        foreach ($toRedis as $key => $value) {
            $this->redisClient->set($key, json_encode($value));
        }

        return true;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getLeaderboard($type)
    {
        if ($redisData = $this->redisClient->get($type)) {
            return json_decode($redisData);
        }

        return $this->em->getRepository(XP::class)->getTopXUsersByType($type);
    }
}
