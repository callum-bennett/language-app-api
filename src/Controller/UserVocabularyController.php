<?php

namespace App\Controller;

use App\Entity\UserVocabulary;
use App\Repository\UserVocabularyRepository;
use App\Service\UserVocabularyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UserVocabularyController.
 *
 * @Route("/api/user_vocabulary", name="api_user_vocabulary_")
 */
class UserVocabularyController extends ApiController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserVocabularyRepository
     */
    private $repository;
    private $serializer;
    /**
     * @var UserVocabularyService
     */
    private $service;

    /**
     * UserVocabularyController constructor.
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, UserVocabularyService $service)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(UserVocabulary::class);
        $this->serializer = $serializer;
        $this->service = $service;
    }

    /**
     * @Route("/", name="user_vocabulary", methods={"GET"})
     */
    public function index()
    {
        $user = $this->getUser();
        $vocabulary = $this->repository->findAll(['user' => $user]);
        $data = $this->serializer->serialize($vocabulary, 'json', [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ["user"],
                AbstractNormalizer::CALLBACKS => [
                        'word' => function ($o) {
                            return $o->getId();
                        },
                ],
        ]);

        return $this->json($data);
    }
}
