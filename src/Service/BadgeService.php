<?php


namespace App\Service;

use App\Entity\Badge;
use App\Entity\User;
use App\Entity\UserBadge;
use App\Event\BadgeAwardedEvent;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BadgeService {

    private $em;

    private $dispatcher;

    /**
     * BadgeService constructor.
     *
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher) {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return array
     */
    private function availableTypes() {
        return [
                BadgeRepository::WORD,
                BadgeRepository::LESSON
        ];
    }

    /**
     * @param User $user
     * @param Badge $badge
     * @return bool
     * @throws \Exception
     */
    public function awardBadge(User $user, Badge $badge) {

        if ($user->getUserBadges()->contains($badge)) {
            throw new \Exception("User already has badge");
        }

        $userBadge = new UserBadge();
        $userBadge->setUser($user);
        $userBadge->setBadge($badge);
        $userBadge->setAwardedDate(time());
        $this->em->persist($userBadge);
        $this->em->flush();

        $event = new BadgeAwardedEvent($userBadge);
        $this->dispatcher->dispatch($event, $event::NAME);

        return true;
    }

    /**
     * @param User $user
     * @param $availableBadges
     * @throws \Exception
     */
    private function checkWordBadgeEligibility(User $user, $availableBadges) {
        $userVocabulary = $user->getUserVocabularies();

        foreach ($availableBadges as $key => $badge) {
            if (sizeof($userVocabulary) >= intval($key)) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    /**
     * @param User $user
     * @param $availableBadges
     */
    private function checkLessonBadgeEligibility(User $user, $availableBadges) {

    }

    /**
     * @param User $user
     * @param $type
     * @throws \Exception
     */
    public function checkBadgeEligibility(User $user, $type) {

        if (!in_array($type, $this->availableTypes())) {
            throw new \Exception("Unknown badge type");
        }

        $badgeRepo = $this->em->getRepository(Badge::class);
        $availableBadges = $badgeRepo->getUnobtainedBadgesByNotifier($user, $type);

        if (empty($availableBadges)) {
            return;
        } else if ($type === "word") {
            $this->checkWordBadgeEligibility($user, $availableBadges);
        } else if ($type === "lesson") {
            $this->checkLessonBadgeEligibility($user, $availableBadges);
        } else {
            // do nothing
        }
    }
}
