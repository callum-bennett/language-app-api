<?php

namespace App\EventSubscriber;

use App\Entity\Badge;
use App\Event\LessonCompletedEvent;
use App\Event\LessonComponentCompletedEvent;
use App\Service\BadgeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BadgeSubscriber implements EventSubscriberInterface
{
    private $badgeService;

    private $em;

    /**
     * BadgeSubscriber constructor.
     *
     * @param BadgeService $badgeService
     * @param EntityManagerInterface $em
     */
    public function __construct(BadgeService $badgeService, EntityManagerInterface $em) {
        $this->badgeService = $badgeService;
        $this->em = $em;
    }

    /**
     * @return array|\array[][]
     */
    public static function getSubscribedEvents()
    {
        return [
                LessonCompletedEvent::NAME => [
                        ['checkCadetBadge', 0],
                ],
                LessonComponentCompletedEvent::NAME => [
                        ['checkNoMistakesBadge', 0]
                ]
        ];
    }

    /**
     * @param LessonCompletedEvent $event
     * @throws \Exception
     */
    public function checkCadetBadge(LessonCompletedEvent $event)
    {
        $user = $event->getUser();

        if ($badge = $this->em->getRepository(Badge::class)->getUnobtainedBadgeForUser($user, "cadet")) {
            $this->badgeService->awardBadge($user, $badge);
        }
    }

    /**
     * @param LessonComponentCompletedEvent $event
     * @throws \Exception
     */
    public function checkNoMistakesBadge(LessonComponentCompletedEvent $event)
    {
        $user = $event->getUser();
        $componentInstance = $event->getLessonComponentInstance();
        $lessonProgress = $event->getLessonProgress();

        if ($badge = $this->em->getRepository(Badge::class)->getUnobtainedBadgeForUser($user, "no_mistakes")) {

            $key = $componentInstance->getLessonComponent()->getShortname();

            $incorrectAnswers = array_filter($lessonProgress->getResponses()[$key], function($item) {
                return !$item;
            });

            if (empty($incorrectAnswers)) {
                $this->badgeService->awardBadge($user, $badge);
            }
        }
    }
}