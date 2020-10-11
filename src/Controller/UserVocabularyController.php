<?php

namespace App\Controller;

use App\Entity\UserVocabulary;
use App\Repository\UserVocabularyRepository;
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
     * UserVocabularyController constructor.
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(UserVocabulary::class);
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="user_vocabulary", methods={"GET"})
     */
    public function index()
    {
        $data = [];

        if ($vocabulary = $this->repository->findAll()) {
            $data = $this->serializer->serialize($vocabulary, 'json', [
                    AbstractNormalizer::CALLBACKS => [
                            'word' => function ($innerObject) {
                                return $innerObject->getId();
                            },
                    ],
            ]);
        }

        return $this->json($data);
    }
}
