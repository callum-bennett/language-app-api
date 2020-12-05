<?php

namespace App\Service;

use App\Entity\Word;
use Doctrine\ORM\EntityManagerInterface;
use Google\ApiCore\ApiException;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WordService
{
    private $em;
    private $params;

    /**
     * WordService constructor.
     *
     * @param ParameterBagInterface $params
     * @param EntityManagerInterface $em
     */
    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=google-credentials.json');

        $this->em = $em;
        $this->params = $params;
    }

    /**
     * @param $text
     * @return string
     * @throws ApiException
     */
    private function textToSpeech(string $text): string {

        $client = new TextToSpeechClient();
        $synthesisInputText = (new SynthesisInput())
                ->setText($text);

        $voice = (new VoiceSelectionParams())
                ->setLanguageCode('es-ES')
                ->setSsmlGender(SsmlVoiceGender::FEMALE);

        $audioConfig = (new AudioConfig())
                ->setAudioEncoding(AudioEncoding::MP3)
                ->setEffectsProfileId(["telephony-class-application"]);

        return $client->synthesizeSpeech($synthesisInputText, $voice, $audioConfig)->getAudioContent();
    }

    /**
     * @param $sound
     * @param $name
     */
    private function uploadSound($sound, $name) {

        $storage = new StorageClient();
        $bucket = $storage->bucket("cb591");
        $bucket->upload($sound, [
                'name' => ltrim($name, "/")
        ]);
    }

    /**
     * @param $name
     * @param $categories
     * @param $translation
     * @return Word
     */
    public function createWord($name, $categories, $translation): Word {

        $word = new Word();
        $word->setName($name);
        $word->setTranslation($translation);
        $word->setIsValid(true);

        foreach ($categories as $category) {
            $word->addCategory($category);
        }

        try {
            $sound = $this->textToSpeech($name);

            $sanitizedName = preg_replace("/[^a-zA-ZÁÉÍÑÓÚÜáéíñóúü]/", "", $name);
            $fileName = $sanitizedName . "-". time() . ".mp3";
            $filePath = "/sounds/" . $fileName;

            $this->uploadSound($sound, $filePath);

            $word->setImageUrl("/images/${name}.jpeg");
            $word->setSoundUri($filePath);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $word->setIsValid(false);
        }

        $this->em->persist($word);
        $this->em->flush();

        return $word;
    }
}
