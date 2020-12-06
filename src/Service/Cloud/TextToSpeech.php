<?php

namespace App\Service\Cloud;

use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TextToSpeech extends GoogleCloudClient
{
    private $client;

    /**
     * TextToSpeech constructor.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        self::setCredentials($params);
        $this->client = new TextToSpeechClient();
    }

    /**
     * @param $text
     * @return string
     * @throws ApiException
     */
    public function execute(string $text): string
    {
        $synthesisInputText = (new SynthesisInput())
                ->setText($text);

        $voice = (new VoiceSelectionParams())
                ->setLanguageCode('es-ES')
                ->setSsmlGender(SsmlVoiceGender::FEMALE);

        $audioConfig = (new AudioConfig())
                ->setAudioEncoding(AudioEncoding::MP3)
                ->setEffectsProfileId(["telephony-class-application"]);

        return $this->client->synthesizeSpeech($synthesisInputText, $voice, $audioConfig)->getAudioContent();
    }
}
