<?php

namespace App\EventSubscriber;

use App\Event\BadgeAwardedEvent;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotificationSubscriber implements EventSubscriberInterface
{
    private $notificationService;

    private $em;

    /**
     * NotificationSubscriber constructor.
     *
     * @param NotificationService $notificationService
     * @param EntityManagerInterface $em
     */
    public function __construct(NotificationService $notificationService, EntityManagerInterface $em) {
        $this->notificationService = $notificationService;
        $this->em = $em;
    }

    /**
     * @return array|\array[][]
     */
    public static function getSubscribedEvents()
    {
        return [
                BadgeAwardedEvent::NAME => [
                        ['addBadgeAwardedNotification', 0],
                ],
        ];
    }

    /**
     * @param BadgeAwardedEvent $event
     */
    public function addBadgeAwardedNotification(BadgeAwardedEvent $event) {

        $user = $event->getUserBadge()->getUser();
        $badge = $event->getUserBadge()->getBadge();
        $message = sprintf("New badge awarded! - %s", $badge->getName());

        $this->notificationService->addUserNotification($user, NotificationService::BADGE, $message);
    }
}