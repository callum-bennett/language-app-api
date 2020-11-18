<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Lesson;
use App\Entity\LessonComponentInstance;
use App\Entity\LessonProgress;
use App\Entity\User;
use App\Entity\Word;
use App\Repository\LessonProgressRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    public function __construct(EntityManagerInterface $em, LessonRepository $lessonRepository, LessonProgressRepository $lessonProgressRepository) {
        $this->em = $em;
        $this->lessonRepository = $lessonRepository;
        $this->lessonProgressRepository = $lessonProgressRepository;
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
     * @return LessonProgress|false
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

            return $lessonProgress;
        }

        return false;
    }

    /**
     * @param LessonProgress $lessonProgress
     * @param Word $word
     * @param $correct
     * @return bool
     */
    public function submitAnswer(LessonProgress $lessonProgress, Word $word, $correct) {

        $key = $lessonProgress->getActiveComponent()->getLessonComponent()->getShortname();

        $currentResponses = $lessonProgress->getResponses();
        if (!array_key_exists($key, $currentResponses)) {
            $currentResponses[$key] = [];
        }
        $currentResponses[$key][$word->getId()] = $correct;
        $lessonProgress->setResponses($currentResponses);
        $this->em->persist($lessonProgress);
        $this->em->flush();

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

        // @todo check can advance

        $lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);
        $currentComponent = $lessonProgress->getActiveComponent();
        $nextComponent = $this->em->getRepository(LessonComponentInstance::class)->findNextLessonComponent($currentComponent);

        if ($nextComponent) {
            $lessonProgress->setActiveComponent($nextComponent);
        } else {
            $lessonProgress->setStatus(self::LESSON_COMPLETE);
        }

        $this->em->persist($lessonProgress);
        $this->em->flush();

        return $lessonProgress;
    }
}