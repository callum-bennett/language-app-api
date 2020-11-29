<?php

namespace App\EventSubscriber;

use App\Event\LessonComponentCompletedEvent;
use App\Service\XPService;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class XPSubscriber implements EventSubscriberInterface
{
    private $xpService;

    /**
     * UserVocabularySubscriber constructor.
     *
     * @param XPService $xpService
     */
    public function __construct(XPService $xpService)
    {
        $this->xpService = $xpService;
    }

    /**
     * @return array|\array[][]
     */
    public static function getSubscribedEvents()
    {
        return [
                LessonComponentCompletedEvent::NAME => [
                        ['updateXp', 0],
                ],
        ];
    }

    /**
     * @param LessonComponentCompletedEvent $event
     * @throws Exception
     */
    public function updateXp(LessonComponentCompletedEvent $event)
    {
        $user = $event->getUser();
        $lessonComponent = $event->getLessonComponentInstance()->getLessonComponent();

        if (!$lessonComponent->getRequiresInput()) {
            return;
        }

        $allResponses = $event->getLessonProgress()->getResponses();

        if ($responses = $allResponses[$lessonComponent->getShortname()]) {

            $correctAnswers = array_filter($responses, function ($response) {
                return $response;
            });

            $points = sizeof($correctAnswers) * XPService::CORRECT_ANSWER_XP_POINTS;

            $this->xpService->updateXP($user, $points);
        }
    }
}
