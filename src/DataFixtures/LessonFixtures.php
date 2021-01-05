<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LessonFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const LESSON_REF_FAMILY_1 = "family_1";
    public const LESSON_REF_FAMILY_2 = "family_2";
    //public const LESSON_REF_FAMILY_3 = "family_3";
    public const LESSON_REF_NUMBERS_1 = "numbers_1";
    public const LESSON_REF_NUMBERS_2 = "numbers_2";
    public const LESSON_REF_TRAVEL_1 = "travel_1";

    public function getDependencies()
    {
        return [
                CategoryFixtures::class,
                WordFixtures::class,
        ];
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getLessons() as [$categoryRef, $sequence, $name, $wordStart, $wordEnd, $ref]) {
            $category = $this->getReference($categoryRef);

            $lesson = new Lesson();
            $lesson->setCategory($category);
            $lesson->setName($name);
            $lesson->setSequence($sequence);

            for ($i = $wordStart; $i < $wordEnd; ++$i) {
                $word = $this->getReference("{$categoryRef}_word_{$i}");
                $lesson->addWord($word);
            }

            $objectManager->persist($lesson);
            $this->addReference($ref, $lesson);
        }

        $objectManager->flush();
    }

    private function getLessons(): array
    {
        return [
            // $lesson = [$categoryRef, $sequence, $name, $wordStart, $wordEnd, $ref];
            [CategoryFixtures::CAT_REF_FAMILY, 0, "Immediate Family", 0, 10, self::LESSON_REF_FAMILY_1],
            [CategoryFixtures::CAT_REF_FAMILY, 1, "Relatives", 10, 20, self::LESSON_REF_FAMILY_2],
            //[CategoryFixtures::CAT_REF_FAMILY, 2, "Relatives II", 20, 29, self::LESSON_REF_FAMILY_3],

            [CategoryFixtures::CAT_REF_NUMBERS, 0, "1-10", 0, 10, self::LESSON_REF_NUMBERS_1],
            [CategoryFixtures::CAT_REF_NUMBERS, 1, "2-20", 10, 20, self::LESSON_REF_NUMBERS_2],

            [CategoryFixtures::CAT_REF_TRAVEL, 0, "Travel I", 0, 10, self::LESSON_REF_TRAVEL_1],
        ];
    }


    public static function getGroups(): array
    {
        return ['production'];
    }
}
