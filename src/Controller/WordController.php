<?php

namespace App\Controller;

use App\Entity\Word;
use App\Repository\WordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class WordController
 * @package App\Controller\Api
 * @Route("/api/word", name="api_word_")
 */
class WordController extends ApiController
{
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
            $data = $this->serializer->serialize($words, "json");
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

        if (!$this->repository->findOneBy(["name" => $name])) {
            $word = new Word();
            $word->setName($name);
            $word->setTranslation($translation);
            $word->setGender($gender);
            $this->em->persist($word);
            $this->em->flush();
            return $this->json(true);
        }

        return $this->json(false);
    }


}
