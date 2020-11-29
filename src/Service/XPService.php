<?php

namespace App\Service;

use App\Entity\XP;
use App\Repository\XPRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class XPService
{
    public const DAILY = "daily";
    public const WEEKLY = "weekly";
    public const MONTHLY = "monthly";

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
}
