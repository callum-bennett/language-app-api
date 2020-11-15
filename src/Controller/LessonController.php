<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\LessonRepository;
use App\Service\LessonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class LessonController.
 *
 * @Route("/api/lesson", name="api_lesson_")
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
        $data = [];

        if ($lessons = $this->repository->findAll()) {
            $data = $this->serializer->serialize($lessons, 'json', [
                    AbstractNormalizer::CALLBACKS => [
                            'category' => function ($innerObject) {
                                return $innerObject->getId();
                            },
                            'lessonComponents' => function ($innerObject) {
                                return $innerObject->getId();
                            },

                    ],
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['words']
            ]);
        }

        return $this->json($data);
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
        } catch (\Exception $e) {
            return $this->json(false, 500);
        }

        return $this->json(true);
    }

    /**
     * @Route("/{id}/finish", name="finish_lesson", methods={"PATCH"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function finish($id)
    {
        try {
            $user = $this->getUser();
            $lesson = $this->repository->find($id);
            $this->service->finishLesson($user, $lesson);
        } catch (\Exception $e) {
            return $this->json(false, 500);
        }

        return $this->json(true);
    }

    /**
     * @Route("/{id}/finishcrossword", name="finish_crossword", methods={"PATCH"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function finishcrossword($id)
    {
        try {
            $user = $this->getUser();
            $lesson = $this->repository->find($id);
            $this->service->finishCrossword($user, $lesson);
        } catch (\Exception $e) {
            return $this->json(false, 500);
        }

        return $this->json(true);
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
        $lesson = $this->repository->findBy($id);
        $data = $this->serializer->serialize($lesson->getProgress(), 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['lesson']]);

        return $this->json($data);
    }
}
