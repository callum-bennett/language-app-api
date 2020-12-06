<?php

namespace App\Service;

use App\Entity\Word;
use App\Service\Cloud\GCSUploader;
use App\Service\Cloud\TextToSpeech;
use Doctrine\ORM\EntityManagerInterface;
use Google\ApiCore\ApiException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WordService
{
    private $em;
    private $params;
    private $gcsUploader;
    private $textToSpeech;

    /**
     * WordService constructor.
     *
     * @param ParameterBagInterface $params
     * @param EntityManagerInterface $em
     * @param GCSUploader $gcsUploader
     * @param TextToSpeech $textToSpeech
     */
    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, GCSUploader $gcsUploader, TextToSpeech $textToSpeech)
    {
        $this->em = $em;
        $this->params = $params;
        $this->gcsUploader = $gcsUploader;
        $this->textToSpeech = $textToSpeech;
    }

    /**
     * @param $soundFile
     * @param $name
     * @return false
     */
    private function uploadSoundFile($soundFile, $name)
    {
        $soundDir = $this->params->get("googleCloudSoundDir");
        $sanitizedName = preg_replace("/[^a-zA-ZÁÉÍÑÓÚÜáéíñóúü]/", "", $name);
        $fileName = $sanitizedName . "-". time() . ".mp3";
        $filePath = $soundDir . $fileName;

        return $this->gcsUploader->upload($soundFile, $filePath);
        ;
    }

    /**
     * @param $name
     * @param $categories
     * @param $translation
     * @return Word
     */
    public function createWord($name, $categories, $translation): Word
    {
        $word = new Word();
        $word->setName($name);
        $word->setTranslation($translation);
        $word->setIsValid(true);

        foreach ($categories as $category) {
            $word->addCategory($category);
        }

        try {
            $soundFile = $this->textToSpeech->execute($name);
            $soundUrl = $this->uploadSoundFile($soundFile, $name);

            $bucketName = $this->params->get("googleCloudBucket");
            $imageDir = $this->params->get("googleCloudImageDir");
            $imagePath = "{$imageDir}words/{$name}.jpg";
            $imageUrl = GCSUploader::$gcsBasePath . $bucketName . "/" . $imagePath;

            $word->setImageUrl($imageUrl);
            $word->setsoundUrl($soundUrl);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $word->setIsValid(false);
        }

        $this->em->persist($word);
        $this->em->flush();

        return $word;
    }
}
