<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\LessonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CategoryController.
 *
 * @Route("/api/category", name="api_category_")
 */
class CategoryController extends ApiController
{
    private $em;
    /**
     * @var CategoryRepository
     */
    private $repository;
    private $serializer;

    /**
     * @var LessonService
     */
    private $lessonService;

    /**
     * CategoryController constructor.
     */
    public function __construct(LessonService $lessonService, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Category::class);
        $this->serializer = $serializer;
        $this->lessonService = $lessonService;
    }

    /**
     * @Route("/", name="category", methods={"GET"})
     */
    public function index()
    {
        $data = [];

        if ($categories = $this->repository->findAll()) {
            $data = $this->serializer->serialize($categories, 'json', [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['words', 'lessons'],
                    AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                        return $object->getId();
                    },
            ]);
        }

        return $this->json($data);
    }

    /**
     * @Route("/", name="create_category", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent());
        $name = $data->name;
        $imageUrl = $data->imageUrl;

        if (!$this->repository->findOneBy(['name' => $name])) {
            $category = new Category();
            $category->setName($name);
            $category->setImageUrl($imageUrl);
            $this->em->persist($category);
            $this->em->flush();

            return $this->json(true);
        }

        return $this->json(false);
    }

    /**
     * @Route("/{id}/words", name="get_words", methods={"GET"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getWords($id)
    {
        $data = [];

        if ($category = $this->repository->find($id)) {
            $data = $this->serializer->serialize($category->getWords(), 'json');
        }

        return $this->json($data);
    }

    /**
     * @Route("/{id}/progress", name="get_progress", methods={"GET"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getProgress($id)
    {
        $user = $this->getUser();
        $category = $this->repository->find($id);
        $progress = $this->lessonService->getUserCategoryProgress($user, $category);

        $objectToId = function ($o) {
            return $o ? $o->getId() : null;
        };

        $data = $this->serializer->serialize($progress, 'json', [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['words', 'user', 'lessonComponentInstances'],
                AbstractNormalizer::CALLBACKS => [
                        'category' => $objectToId,
                        'lesson' => $objectToId,
                        'activeComponent' => $objectToId
                ],
        ]);

        return $this->json($data);
    }

}
