<?php

namespace App\DataFixtures;

use App\Entity\Word;
use App\Service\WordService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WordFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private $wordService;
    private $params;

    public function __construct(WordService $wordService, ParameterBagInterface $params)
    {
        $this->wordService = $wordService;
        $this->params = $params;
    }

    public function getDependencies()
    {
        return [
                CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $objectManager): void
    {
        $i = 0;
        foreach ($this->getWords() as [$name, $categoryRefs, $translation, $imageUrl]) {
            $word = new Word();
            $word->setName($name);
            $word->setTranslation($translation);
            $word->setImageUrl($imageUrl);
            $word->setIsValid(true);

            try {
                $sound = $this->wordService->textToSpeech($name);

                $publicDir = $this->params->get("publicSoundDir");
                $sanitizedName = preg_replace("/[^a-zA-ZÁÉÍÑÓÚÜáéíñóúü]/", "", $name);
                $fileName = $sanitizedName . "-". time() . ".mp3";
                $filePath = "/words/" . $fileName;

                file_put_contents($publicDir . $filePath, $sound);
                $word->setSoundUri($filePath);
            } catch (\Exception $e) {
                echo $e->getMessage();
                $word->setIsValid(false);
            }

            foreach ($categoryRefs as $ref) {
                $category = $this->getReference($ref);
                $word->addCategory($category);
            }

            $objectManager->persist($word);
            $this->addReference("word_$i", $word);
            ++$i;
        }

        $objectManager->flush();
    }

    private function getWords(): array
    {
        return [
            // $word = [$name, $categoryRefs, $translation, $imageUrl];

            // Family
            ['yo', [CategoryFixtures::CAT_REF_FAMILY], 'Me', 'https://i.ibb.co/ScYTFSW/yo.jpg'],
            ['madre', [CategoryFixtures::CAT_REF_FAMILY], 'Mother', 'https://i.ibb.co/PckrX5R/madre.jpg'],
            ['padre', [CategoryFixtures::CAT_REF_FAMILY], 'Father', 'https://i.ibb.co/18mkc6x/padre.jpg'],
            ['niño', [CategoryFixtures::CAT_REF_FAMILY], 'Boy', 'https://i.ibb.co/XZm363B/nino.jpg'],
            ['niña', [CategoryFixtures::CAT_REF_FAMILY], 'Girl', 'https://i.ibb.co/7vGGWg9/nina.jpg'],
            ['hijo', [CategoryFixtures::CAT_REF_FAMILY], 'Son', 'https://i.ibb.co/QpkvX4s/hijo.jpg'],
            ['hija', [CategoryFixtures::CAT_REF_FAMILY], 'Daughter', 'https://i.ibb.co/K79GKQ2/hija.jpg'],
            ['bebe', [CategoryFixtures::CAT_REF_FAMILY], 'Baby', 'https://i.ibb.co/F0Fw8zy/bebe.jpg'],
            ['hermana', [CategoryFixtures::CAT_REF_FAMILY], 'Sister', 'https://i.ibb.co/9N1yph5/sister.jpg'],
            ['hermano', [CategoryFixtures::CAT_REF_FAMILY], 'Brother', 'https://i.ibb.co/YXWzqjS/hermano.jpg'],
            ['abuelo', [CategoryFixtures::CAT_REF_FAMILY], 'Grandfather', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['abuela', [CategoryFixtures::CAT_REF_FAMILY], 'Grandmother', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['primo', [CategoryFixtures::CAT_REF_FAMILY], 'Cousin (female]', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['prima', [CategoryFixtures::CAT_REF_FAMILY], 'Cousin (male]', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['sobrino', [CategoryFixtures::CAT_REF_FAMILY], 'Nephew', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['sobrina', [CategoryFixtures::CAT_REF_FAMILY], 'Niece', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['nieto', [CategoryFixtures::CAT_REF_FAMILY], 'Grandson', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['nieta', [CategoryFixtures::CAT_REF_FAMILY], 'Granddaughter', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['cuñado', [CategoryFixtures::CAT_REF_FAMILY], 'Brother in law', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['cuñada', [CategoryFixtures::CAT_REF_FAMILY], 'Sister in law', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['suegro', [CategoryFixtures::CAT_REF_FAMILY], 'Father in law', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['suegra', [CategoryFixtures::CAT_REF_FAMILY], 'Mother in law', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['esposo', [CategoryFixtures::CAT_REF_FAMILY], 'Husband', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['esposa', [CategoryFixtures::CAT_REF_FAMILY], 'Wife', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['gemelo', [CategoryFixtures::CAT_REF_FAMILY], 'Twin', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['madrina', [CategoryFixtures::CAT_REF_FAMILY], 'Godmother', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['padrino', [CategoryFixtures::CAT_REF_FAMILY], 'Godfather', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['mama', [CategoryFixtures::CAT_REF_FAMILY], 'Mum', 'https://i.ibb.co/x35kDv2/blank.jpg'],
            ['papa', [CategoryFixtures::CAT_REF_FAMILY], 'Dad', 'https://i.ibb.co/x35kDv2/blank.jpg'],

            // Top 100 words
            //['que', [CategoryFixtures::CAT_REF_TOP_100_WORDS], 'That/Than'],
            //['de', [CategoryFixtures::CAT_REF_TOP_100_WORDS], 'Of'],
            //['no', [CategoryFixtures::CAT_REF_TOP_100_WORDS], 'No'],
            //['a', [CategoryFixtures::CAT_REF_TOP_100_WORDS], 'To'],
            //['la', [CategoryFixtures::CAT_REF_TOP_100_WORDS], 'The (feminine]'],
            //['el', [CategoryFixtures::CAT_REF_TOP_100_WORDS], 'The (masculine]'],
            //['es', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['y', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['en', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['lo', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['un', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['por', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['qué', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['me', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['una', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['te', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['los', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['se', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['con', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['para', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['mi', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['está', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['si', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['bien', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['pero', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['yo', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['eso', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['las', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['sí', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
            //['su', [CategoryFixtures::CAT_REF_TOP_100_WORDS], ''],
        ];
    }

    public static function getGroups(): array
    {
        return ['production'];
    }
}
