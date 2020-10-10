<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Word;
use App\Entity\UserVocabulary;
use App\Repository\WordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class WordController
 * @package App\Controller\Api
 * @Route("/api/word", name="api_word_")
 */
class WordController extends ApiController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var WordRepository
     */
    private $repository;
    private $serializer;

    /**
     * WordController constructor.
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer) {
        $this->em = $em;
        $this->repository = $em->getRepository(Word::class);
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="word", methods={"GET"})
     */
    public function index()
    {
        $data = [];

        if ($words = $this->repository->findAll()) {
            $data = $this->serializer->serialize($words, "json", [
                    AbstractNormalizer::CALLBACKS => [
                            'category' => function (PersistentCollection $collection) {
                                return array_map(function($object) {
                                    return $object->getId();
                                }, $collection->getValues());
                            },
                            'lesson' => function ($object) {
                                return $object ? $object->getId() : null;
                            }
                    ]
            ]);
        }

        return $this->json($data);
    }

    /**
     * @Route("/", name="create_word", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent());
        $name = $data->name;
        $translation = $data->translation;
        $gender = $data->gender;
        $categoryIds = $data->categoryIds;

        if (!$this->repository->findOneBy(["name" => $name])) {
            $word = new Word();
            $word->setName($name);
            $word->setTranslation($translation);
            $word->setGender($gender);
            foreach ($categoryIds as $categoryId) {
                if ($category = $this->em->getRepository(Category::class)->find($categoryId)) {
                    $word->addCategory($category);
                }
            }
            $this->em->persist($word);
            $this->em->flush();
            return $this->json(true);
        }

        return $this->json(false);
    }

    /**
     * @Route("/{id}/mark_seen", name="mark_seen", methods={"POST"})
     * @param $id
     * @return JsonResponse
     */
    public function mark_seen($id) {

        $word = $this->repository->find($id);

        if (!$vocabEntry = $this->em->getRepository(UserVocabulary::class)->findOneBy(['word' => $word])) {
            $vocabEntry = new UserVocabulary();
            $vocabEntry->setWord($word);
            $vocabEntry->setTimeCreated(time());
            $this->em->persist($vocabEntry);
            $this->em->flush();

            return $this->json(true);
        }

        return $this->json(false);
    }



    /**
     * @Route("/{id}/attempt", name="attempt_word", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function attempt($id, Request $request) {

        $data = json_decode($request->getContent());

        $correct = $data->status;

        $word = $this->repository->find($id);
        //@ todo tidy this up
        if (!$attempt = $this->em->getRepository(UserVocabulary::class)->findOneBy(['word' => $word])) {
            return false;
        }
        if ($correct) {
            $existingCount = $attempt->getCorrect();
            $attempt->setCorrect(++$existingCount);
        } else {
            $existingCount = $attempt->getWrong();
            $attempt->setWrong(++$existingCount);
        }
        $attempt->setLastAttempt(time());

        $this->em->persist($attempt);
        $this->em->flush();
        return $this->json(true);
    }
}
