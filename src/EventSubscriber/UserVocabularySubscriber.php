<?php

namespace App\EventSubscriber;

use App\Event\WordLearnedEvent;
use App\Repository\BadgeRepository;
use App\Service\BadgeService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class   UserVocabularySubscriber implements EventSubscriberInterface
{
    private $badgeService;

    /**
     * UserVocabularySubscriber constructor.
     *
     * @param BadgeService $badgeService
     */
    public function __construct(BadgeService $badgeService) {
        $this->badgeService = $badgeService;
    }

    /**
     * @return array|\array[][]
     */
    public static function getSubscribedEvents()
    {
        return [
                WordLearnedEvent::NAME => [
                        ['checkBadges', 0],
                ],
        ];
    }

    /**
     * @param WordLearnedEvent $event
     * @throws \Exception
     */
    public function checkBadges(WordLearnedEvent $event)
    {
        $user = $event->getVocabItem()->getUser();
        $this->badgeService->checkBadgeEligibility($user, BadgeRepository::WORD);
    }
}