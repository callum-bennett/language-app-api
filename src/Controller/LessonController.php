<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Word;
use App\Repository\LessonRepository;
use App\Service\LessonService;
use App\Service\UserVocabularyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class LessonController.
 *
 * @Route("/api/v1/lesson", name="api_v1_lesson_")
 */
class LessonController extends ApiController
{
    private $em;
    /**
     * @var LessonRepository
     */
    private $repository;
    private $serializer;

    /**
     * @var LessonService
     */
    private $service;

    /**
     * LessonController constructor.
     */
    public function __construct(LessonService $service, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Lesson::class);
        $this->service = $service;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="lesson", methods={"GET"})
     */
    public function index()
    {
        try {
            $data = [];

            if ($lessons = $this->repository->findAll()) {
                $data = $this->serializer->serialize($lessons, 'json', [
                        AbstractNormalizer::IGNORED_ATTRIBUTES => ['words', 'lessonComponentInstances'],
                        AbstractNormalizer::CALLBACKS => [
                                'category' => function ($innerObject) {
                                    return $innerObject->getId();
                                },
                        ],
                ]);
            }

            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @Route("/{id}/start", name="start_lesson", methods={"PATCH"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function start($id)
    {
        try {
            $user = $this->getUser();
            $lesson = $this->repository->find($id);
            $this->service->startLesson($user, $lesson);

            return $this->success(true);
        } catch (\Exception $e) {
            return $this->error(false, 500);
        }
    }

    /**
     * @Route("/{id}/submitAnswer", name="submit_answer", methods={"POST"})
     *
     * @param $id
     *
     * @param Request $request
     * @param UserVocabularyService $vocabularyService
     * @return JsonResponse
     */
    public function submitAnswer($id, Request $request, UserVocabularyService $vocabularyService)
    {
        try {
            $data = json_decode($request->getContent());
            $wordId = $data->wordId;
            $correct = $data->correct;

            $user = $this->getUser();
            $lesson = $this->repository->find($id);

            if (!$lessonProgress = $this->service->getUserLessonInstance($user, $lesson)) {
                // @todo exception
                return false;
            }
            $word = $this->em->getRepository(Word::class)->find($wordId);
            $result = $this->service->submitAnswer($lessonProgress, $word, $correct, $vocabularyService);

            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @Route("/{id}/advance", name="advance_lesson", methods={"PATCH"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function advance($id)
    {
        $objectToId = function ($o) {
            return $o ? $o->getId() : null;
        };

        try {
            $user = $this->getUser();
            $lesson = $this->repository->find($id);
            $lessonProgress = $this->service->advanceLesson($user, $lesson);

            $data = $this->serializer->serialize($lessonProgress, 'json', [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
                    AbstractNormalizer::CALLBACKS => [
                            'lesson' => $objectToId,
                        //@ todo move component data to front end
                            'activeComponent' => function ($o) {
                                return $o ? $o->getLessonComponent()->getId() : null;
                            }
                    ],
            ]);

            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @Route("/{id}/progress", name="get_lesson_progress", methods={"GET"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function get_progress($id)
    {
        try {
            $lesson = $this->repository->findBy($id);
            $data = $this->serializer->serialize($lesson->getProgress(), 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['lesson']]);

            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
