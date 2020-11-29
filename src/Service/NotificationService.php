<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public const BADGE = "badge";

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param $type
     * @param $message
     */
    public function addNotification(User $user, string $type, string $message)
    {
        $currentNotifications = $user->getNotifications();

        if (is_null($currentNotifications)) {
            $currentNotifications = [];
        }

        if (empty($currentNotifications[$type])) {
            $currentNotifications[$type] = [];
        }

        $currentNotifications[$type][] = $message;

        $user->setNotifications($currentNotifications);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @param string $type
     */
    public function clearNotificationsOfType(User $user, string $type)
    {
        $currentNotifications = $user->getNotifications();
        unset($currentNotifications[$type]);

        $user->setNotifications($currentNotifications);
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }
}
