<?php

namespace App\DataFixtures;

use App\Entity\Word;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WordFixtures extends Fixture implements DependentFixtureInterface {

    public function getDependencies() {
        return array(
                CategoryFixtures::class,
        );
    }

    public function load(ObjectManager $objectManager): void {

        $i = 0;
        foreach ($this->getWords() as [$name, $categoryRefs, $translation]) {

            $word = new Word();
            $word->setName($name);
            $word->setTranslation($translation);
            foreach ($categoryRefs as $ref) {
                $category = $this->getReference($ref);
                $word->addCategory($category);
            }

            $objectManager->persist($word);
            $this->addReference("word_$i", $word);
            $i++;
        }

        $objectManager->flush();
    }

    private function getWords(): array {
        return [
            // $word = [$name, $categoryRefs, $translation];

            // Family
            ["yo", [CategoryFixtures::CAT_REF_FAMILY], "Me"],
            ["madre", [CategoryFixtures::CAT_REF_FAMILY], "Mother"],
            ["padre", [CategoryFixtures::CAT_REF_FAMILY], "Father"],
            ["niño", [CategoryFixtures::CAT_REF_FAMILY], "Boy"],
            ["niña", [CategoryFixtures::CAT_REF_FAMILY], "Girl"],
            ["hijo", [CategoryFixtures::CAT_REF_FAMILY], "Son"],
            ["hija", [CategoryFixtures::CAT_REF_FAMILY], "Daughter"],
            ["bebé", [CategoryFixtures::CAT_REF_FAMILY], "Baby"],
            ["hermana", [CategoryFixtures::CAT_REF_FAMILY], "Sister"],
            ["hermano", [CategoryFixtures::CAT_REF_FAMILY], "Brother"],
            ["abuelo", [CategoryFixtures::CAT_REF_FAMILY], "Grandfather"],
            ["abuela", [CategoryFixtures::CAT_REF_FAMILY], "Grandmother"],
            ["primo", [CategoryFixtures::CAT_REF_FAMILY], "Cousin (female]"],
            ["prima", [CategoryFixtures::CAT_REF_FAMILY], "Cousin (male]"],
            ["sobrino", [CategoryFixtures::CAT_REF_FAMILY], "Nephew"],
            ["sobrina", [CategoryFixtures::CAT_REF_FAMILY], "Niece"],
            ["nieto", [CategoryFixtures::CAT_REF_FAMILY], "Grandson"],
            ["nieta", [CategoryFixtures::CAT_REF_FAMILY], "Granddaughter"],
            ["cuñado", [CategoryFixtures::CAT_REF_FAMILY], "Brother in law"],
            ["cuñada", [CategoryFixtures::CAT_REF_FAMILY], "Sister in law"],
            ["suegro", [CategoryFixtures::CAT_REF_FAMILY], "Father in law"],
            ["suegra", [CategoryFixtures::CAT_REF_FAMILY], "Mother in law"],
            ["esposo", [CategoryFixtures::CAT_REF_FAMILY], "Husband"],
            ["esposa", [CategoryFixtures::CAT_REF_FAMILY], "Wife"],
            ["gemelo", [CategoryFixtures::CAT_REF_FAMILY], "Twin"],
            ["madrina", [CategoryFixtures::CAT_REF_FAMILY], "Godmother"],
            ["padrino", [CategoryFixtures::CAT_REF_FAMILY], "Godfather"],
            ["mama", [CategoryFixtures::CAT_REF_FAMILY], "Mum"],
            ["papa", [CategoryFixtures::CAT_REF_FAMILY], "Dad"],

            // Top 100 words
            ["que", [CategoryFixtures::CAT_REF_TOP_100_WORDS], "That/Than"],
            ["de", [CategoryFixtures::CAT_REF_TOP_100_WORDS], "Of"],
            ["no", [CategoryFixtures::CAT_REF_TOP_100_WORDS], "No"],
            ["a", [CategoryFixtures::CAT_REF_TOP_100_WORDS], "To"],
            ["la", [CategoryFixtures::CAT_REF_TOP_100_WORDS], "The (feminine]"],
            ["el", [CategoryFixtures::CAT_REF_TOP_100_WORDS], "The (masculine]"],
            ["es", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["y", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["en", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["lo", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["un", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["por", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["qué", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["me", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["una", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["te", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["los", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["se", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["con", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["para", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["mi", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["está", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["si", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["bien", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["pero", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["yo", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["eso", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["las", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["sí", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
            ["su", [CategoryFixtures::CAT_REF_TOP_100_WORDS], ""],
        ];
    }
}
