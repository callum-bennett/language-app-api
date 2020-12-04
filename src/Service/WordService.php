<?php

namespace App\Service;

use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WordService
{
    private $params;

    /**
     * WordService constructor.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @param $text
     * @throws ApiException
     */
    public function textToSpeech($text)
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=/home/callum/uni/Project/assets/Languagelearningapp-c0fe9177a8be.json');

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
}
