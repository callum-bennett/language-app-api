<?php

namespace App\DataFixtures;

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
        foreach ($this->getWords() as [$name, $categoryRefs, $translation]) {
            $category = $categoryRefs[0];

            if (!empty($lastCategory) && $category !== $lastCategory) {
                $i = 0;
            }

            $categories = array_map(function ($ref) {
                return $this->getReference($ref);
            }, $categoryRefs);

            $word = $this->wordService->createWord($name, $categories, $translation);

            $category = $categoryRefs[0];
            $this->addReference("{$category}_word_{$i}", $word);
            ++$i;
            $lastCategory = $category;
        }
    }

    private function getWords(): array
    {
        return [
            // $word = [$name, $categoryRefs, $translation];

            // Family
            ['yo', [CategoryFixtures::CAT_REF_FAMILY], 'Me'],
            ['madre', [CategoryFixtures::CAT_REF_FAMILY], 'Mother'],
            ['padre', [CategoryFixtures::CAT_REF_FAMILY], 'Father'],
            ['niño', [CategoryFixtures::CAT_REF_FAMILY], 'Boy'],
            ['niña', [CategoryFixtures::CAT_REF_FAMILY], 'Girl'],
            ['hijo', [CategoryFixtures::CAT_REF_FAMILY], 'Son'],
            ['hija', [CategoryFixtures::CAT_REF_FAMILY], 'Daughter'],
            ['bebe', [CategoryFixtures::CAT_REF_FAMILY], 'Baby'],
            ['hermana', [CategoryFixtures::CAT_REF_FAMILY], 'Sister'],
            ['hermano', [CategoryFixtures::CAT_REF_FAMILY], 'Brother'],

            ['abuelo', [CategoryFixtures::CAT_REF_FAMILY], 'Grandfather'],
            ['abuela', [CategoryFixtures::CAT_REF_FAMILY], 'Grandmother'],
            ['primo', [CategoryFixtures::CAT_REF_FAMILY], 'Cousin (male)'],
            ['prima', [CategoryFixtures::CAT_REF_FAMILY], 'Cousin (female)'],
            ['sobrino', [CategoryFixtures::CAT_REF_FAMILY], 'Nephew'],
            ['sobrina', [CategoryFixtures::CAT_REF_FAMILY], 'Niece'],
            ['nieto', [CategoryFixtures::CAT_REF_FAMILY], 'Grandson'],
            ['nieta', [CategoryFixtures::CAT_REF_FAMILY], 'Granddaughter'],
            ['cuñado', [CategoryFixtures::CAT_REF_FAMILY], 'Brother in law'],
            ['cuñada', [CategoryFixtures::CAT_REF_FAMILY], 'Sister in law'],

            ['suegro', [CategoryFixtures::CAT_REF_FAMILY], 'Father in law'],
            ['suegra', [CategoryFixtures::CAT_REF_FAMILY], 'Mother in law'],
            ['esposo', [CategoryFixtures::CAT_REF_FAMILY], 'Husband'],
            ['esposa', [CategoryFixtures::CAT_REF_FAMILY], 'Wife'],
            ['gemelo', [CategoryFixtures::CAT_REF_FAMILY], 'Twin'],
            ['madrina', [CategoryFixtures::CAT_REF_FAMILY], 'Godmother'],
            ['padrino', [CategoryFixtures::CAT_REF_FAMILY], 'Godfather'],
            ['mama', [CategoryFixtures::CAT_REF_FAMILY], 'Mum'],
            ['papa', [CategoryFixtures::CAT_REF_FAMILY], 'Dad'],

            // travel
            ['autobús', [CategoryFixtures::CAT_REF_TRAVEL], 'Bus'],
            ['carro', [CategoryFixtures::CAT_REF_TRAVEL], 'Car'],
            ['tren', [CategoryFixtures::CAT_REF_TRAVEL], 'Train'],
            ['aeropuerto', [CategoryFixtures::CAT_REF_TRAVEL], 'Airport'],
            ['viajar', [CategoryFixtures::CAT_REF_TRAVEL], 'To travel'],
            ['playa', [CategoryFixtures::CAT_REF_TRAVEL], 'Beach'],
            ['hotel', [CategoryFixtures::CAT_REF_TRAVEL], 'Hotel'],
            ['vacación', [CategoryFixtures::CAT_REF_TRAVEL], 'Holiday'],
            ['subte', [CategoryFixtures::CAT_REF_TRAVEL], 'Subway'],
            ['taxi', [CategoryFixtures::CAT_REF_TRAVEL], 'Taxi'],


            //['pasaporte', [CategoryFixtures::CAT_REF_TRAVEL], 'Passport'],


            // numbers
            ['uno', [CategoryFixtures::CAT_REF_NUMBERS], 'One'],
            ['dos', [CategoryFixtures::CAT_REF_NUMBERS], 'Two'],
            ['tres', [CategoryFixtures::CAT_REF_NUMBERS], 'Three'],
            ['cuatro', [CategoryFixtures::CAT_REF_NUMBERS], 'Four'],
            ['cinco', [CategoryFixtures::CAT_REF_NUMBERS], 'Five'],
            ['seis', [CategoryFixtures::CAT_REF_NUMBERS], 'Six'],
            ['siete', [CategoryFixtures::CAT_REF_NUMBERS], 'Seven'],
            ['ocho', [CategoryFixtures::CAT_REF_NUMBERS], 'Eight'],
            ['nueve', [CategoryFixtures::CAT_REF_NUMBERS], 'Nine'],
            ['diez', [CategoryFixtures::CAT_REF_NUMBERS], 'Ten'],
            ['once', [CategoryFixtures::CAT_REF_NUMBERS], 'Eleven'],
            ['doce', [CategoryFixtures::CAT_REF_NUMBERS], 'Twelve'],
            ['trece', [CategoryFixtures::CAT_REF_NUMBERS], 'Thirteen'],
            ['catorce', [CategoryFixtures::CAT_REF_NUMBERS], 'Fourteen'],
            ['quince', [CategoryFixtures::CAT_REF_NUMBERS], 'Fifteen'],
            ['dieciseis', [CategoryFixtures::CAT_REF_NUMBERS], 'Sixteen'],
            ['diecisiete', [CategoryFixtures::CAT_REF_NUMBERS], 'Seventeen'],
            ['dieciocho', [CategoryFixtures::CAT_REF_NUMBERS], 'Eighteen'],
            ['diecinueve', [CategoryFixtures::CAT_REF_NUMBERS], 'Nineteen'],
            ['veinte', [CategoryFixtures::CAT_REF_NUMBERS], 'Twenty'],

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
