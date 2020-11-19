<?php

namespace App\Controller;

use App\Entity\LessonComponent;
use App\Repository\LessonRepository;
use App\Service\LessonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class LessonComponentController.
 *
 * @Route("/api/v1/lesson_component", name="api_v1_lesson_")
 */
class LessonComponentController extends ApiController
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
     *
     * @param LessonService $service
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(LessonService $service, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(LessonComponent::class);
        $this->service = $service;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="lesson_component", methods={"GET"})
     */
    public function index()
    {
        $data = [];

        if ($lessonComponents = $this->repository->findAll()) {
            $data = $this->serializer->serialize($lessonComponents, 'json',      [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['lessonComponentInstances']

                    ]);
        }

        return $this->json($data);
    }
}
