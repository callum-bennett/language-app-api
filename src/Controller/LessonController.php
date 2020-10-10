<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class LessonController
 * @package App\Controller\Api
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
     * LessonController constructor.
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer) {
        $this->em = $em;
        $this->repository = $em->getRepository(Lesson::class);
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="lesson", methods={"GET"})
     */
    public function index()
    {
        $data = [];

        if ($lessons = $this->repository->findAll()) {
            $data = $this->serializer->serialize($lessons, "json", [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category', 'words']]);
        }

        return $this->json($data);
    }

    /**
     * @Route("/{id}/progress", name="get_lesson_progress", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function get_progress($id)
    {
        $lesson = $this->repository->findBy($id);
        $data = $this->serializer->serialize($lesson->getProgress(), "json", [AbstractNormalizer::IGNORED_ATTRIBUTES => ['lesson']]);

        return $this->json($data);
    }
}
