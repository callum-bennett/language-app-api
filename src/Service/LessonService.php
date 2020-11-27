<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Lesson;
use App\Entity\LessonComponentInstance;
use App\Entity\LessonProgress;
use App\Entity\User;
use App\Entity\Word;
use App\Event\LessonCompletedEvent;
use App\Event\LessonComponentCompletedEvent;
use App\Repository\LessonProgressRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LessonService {

    const LESSON_STARTED = 0;
    const LESSON_COMPLETE = 1;

    /**
     * @var LessonRepository
     */
    private $lessonRepository;
    /**
     * @var LessonProgressRepository
     */
    private $lessonProgressRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, LessonRepository $lessonRepository, LessonProgressRepository $lessonProgressRepository, EventDispatcherInterface $dispatcher) {
        $this->em = $em;
        $this->lessonRepository = $lessonRepository;
        $this->lessonProgressRepository = $lessonProgressRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param User $user
     * @param Category $category
     * @return array
     */
    public function getUserCategoryProgress(User $user, Category $category) {

        $lessons = $this->lessonRepository->findBy(['category' => $category]);
        $data = [
                'lessons' => $lessons,
                'lessonProgress' => []
        ];

        foreach ($lessons as $lesson) {
            $data['lessons'][] = $lesson;
            if ($lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson])) {
                $data['lessonProgress'][] = $lessonProgress;
            }
        }

        return $data;
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @return LessonProgress
     */
    public function startLesson(User $user, Lesson $lesson) {

        $lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);

        if (!$lessonProgress) {
            $firstComponent = $this->em->getRepository(LessonComponentInstance::class)->findOneBy(['lesson' => $lesson, 'sequence' => 0]);

            $lessonProgress = new LessonProgress();
            $lessonProgress->setUser($user);
            $lessonProgress->setLesson($lesson);
            $lessonProgress->setActiveComponent($firstComponent);
            $lessonProgress->setStatus(self::LESSON_STARTED);
            $this->em->persist($lessonProgress);
            $this->em->flush();
        }

        return $lessonProgress;
    }

    /**
     * @param LessonProgress $lessonProgress
     * @param Word $word
     * @param $correct
     * @param UserVocabularyService $vocabularyService
     * @return bool
     */
    public function submitAnswer(LessonProgress $lessonProgress, Word $word, $correct, UserVocabularyService $vocabularyService) {

        $updateVocabulary = true;

        $key = $lessonProgress->getActiveComponent()->getLessonComponent()->getShortname();

        $currentResponses = $lessonProgress->getResponses();
        if (!array_key_exists($key, $currentResponses)) {
            $currentResponses[$key] = [];
        } else if (array_key_exists($word->getId(), $currentResponses[$key])) {
            $updateVocabulary = false;
        }
        $currentResponses[$key][$word->getId()] = $correct;
        $lessonProgress->setResponses($currentResponses);
        $this->em->persist($lessonProgress);
        $this->em->flush();

        if ($updateVocabulary) {
            $vocabularyService->attemptWord($lessonProgress->getUser(), $word, $correct);
        }

        return true;
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @return mixed
     */
    public function getUserLessonInstance(User $user, Lesson $lesson) {
        return $this->em->getRepository(LessonProgress::class)->findOneBy(['lesson' => $lesson, 'user' => $user]);
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @return LessonProgress|null
     */
    public function advanceLesson(User $user, Lesson $lesson) {

        $lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);
        $currentComponent = $lessonProgress->getActiveComponent();
        $nextComponent = $this->em->getRepository(LessonComponentInstance::class)->findNextLessonComponent($currentComponent);

        $this->completeLessonComponent($lessonProgress, $currentComponent, $nextComponent);

        if (!$nextComponent) {
            $this->completeLesson($lessonProgress);
        }

        $this->em->persist($lessonProgress);
        $this->em->flush();

        return $lessonProgress;
    }

    /**
     * @param $lessonProgress
     * @param $currentComponent
     * @param $nextComponent
     * @return bool
     */
    private function completeLessonComponent(LessonProgress $lessonProgress, LessonComponentInstance $currentComponent,
            LessonComponentInstance $nextComponent = null) {

        $lessonProgress->setActiveComponent($nextComponent);
        $this->em->persist($lessonProgress);

        $event = new LessonComponentCompletedEvent($lessonProgress->getUser(), $currentComponent, $lessonProgress);
        $this->dispatcher->dispatch($event, $event::NAME);

        return true;
    }

    /**
     * @param LessonProgress $lessonProgress
     * @return bool
     */
    private function completeLesson(LessonProgress $lessonProgress) {

        $lessonProgress->setStatus(self::LESSON_COMPLETE);
        $this->em->persist($lessonProgress);

        $event = new LessonCompletedEvent($lessonProgress->getUser(), $lessonProgress);
        $this->dispatcher->dispatch($event, $event::NAME);

        return true;
    }
}