<?php

namespace App\Service;


use App\Entity\Category;
use App\Entity\Lesson;
use App\Entity\LessonProgress;
use App\Entity\User;
use App\Repository\LessonProgressRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;

class LessonService {

    const LESSON_STARTED = 1;
    const LESSON_COMPLETE = 2;
    const CROSSWORD_COMPLETE = 3;
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
     * @return bool
     */
    public function startLesson(User $user, Lesson $lesson) {

        $lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);

        if (!$lessonProgress) {
            $lessonProgress = new LessonProgress();
            $lessonProgress->setUser($user);
            $lessonProgress->setLesson($lesson);
            $lessonProgress->setStatus(self::LESSON_STARTED);
            $this->em->persist($lessonProgress);
            $this->em->flush();
        }

        return true;
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @return bool
     */
    public function finishLesson(User $user, Lesson $lesson) {
        $lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);
        $lessonProgress->setStatus(self::LESSON_COMPLETE);
        $this->em->persist($lessonProgress);
        $this->em->flush();

        return true;
    }

    /**
     * @param User $user
     * @param Lesson $lesson
     * @return bool
     */
    public function finishCrossword(User $user, Lesson $lesson) {
        $lessonProgress = $this->lessonProgressRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);
        $lessonProgress->setStatus(self::CROSSWORD_COMPLETE);
        $this->em->persist($lessonProgress);
        $this->em->flush();

        return true;
    }
}