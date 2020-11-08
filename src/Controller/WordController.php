<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\UserVocabulary;
use App\Entity\Word;
use App\Repository\WordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class WordController.
 *
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
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
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
            $data = $this->serializer->serialize($words, 'json', [
                    AbstractNormalizer::CALLBACKS => [
                            'category' => function (PersistentCollection $collection) {
                                return array_map(function ($object) {
                                    return $object->getId();
                                }, $collection->getValues());
                            },
                            'lesson' => function ($object) {
                                return $object ? $object->getId() : null;
                            },
                    ],
            ]);
        }

        return $this->json($data);
    }

    /**
     * @Route("/", name="create_word", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent());
        $name = $data->name;
        $translation = $data->translation;
        $gender = $data->gender;
        $categoryIds = $data->categoryIds;

        if (!$this->repository->findOneBy(['name' => $name])) {
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
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function mark_seen($id)
    {
        $user = $this->getUser();
        $word = $this->repository->find($id);

        if (!$vocabEntry = $this->em->getRepository(UserVocabulary::class)->findOneBy(['word' => $word])) {
            $vocabEntry = new UserVocabulary();
            $vocabEntry->setUser($user);
            $vocabEntry->setWord($word);
            $vocabEntry->setTimeCreated(time());
            $this->em->persist($vocabEntry);
            $this->em->flush();

            return $this->json(true);
        }

        return $this->json(false);
    }

    /**
     * @Route("/{id}/listen", name="listen", methods={"GET"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function listen($id)
    {
        $word = $this->repository->find($id);

        // instantiates a client
        $client = new TextToSpeechClient();
        $synthesisInputText = (new SynthesisInput())
                ->setText($word->getName());

        $voice = (new VoiceSelectionParams())
                ->setLanguageCode('en-US')
                ->setSsmlGender(SsmlVoiceGender::MALE);

        $effectsProfileId = "telephony-class-application";

        $audioConfig = (new AudioConfig())
                ->setAudioEncoding(AudioEncoding::MP3)
                ->setEffectsProfileId(array($effectsProfileId));

        $response = $client->synthesizeSpeech($synthesisInputText, $voice, $audioConfig);
        $audioContent = $response->getAudioContent();

        $directory = $this->getParameter("publicSoundDir");
        file_put_contents($directory . "/{$word->getName()}.mp3", $audioContent);

        return $this->json(false);
    }

    /**
     * @Route("/{id}/attempt", name="attempt_word", methods={"PUT"})
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function attempt($id, Request $request)
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent());

        $correct = $data->status;

        $word = $this->repository->find($id);
        //@ todo tidy this up
        if (!$vocabItem = $this->em->getRepository(UserVocabulary::class)->findOneBy(['user' => $user, 'word' => $word])) {
            return false;
        }
        if ($correct) {
            $existingCount = $vocabItem->getCorrect();
            $vocabItem->setCorrect(++$existingCount);
        } else {
            $existingCount = $vocabItem->getWrong();
            $vocabItem->setWrong(++$existingCount);
        }
        $vocabItem->setLastAttempt(time());

        $this->em->persist($vocabItem);
        $this->em->flush();

        return $this->json(true);
    }
}
