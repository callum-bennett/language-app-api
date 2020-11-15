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
    public const LESSON_REF_FAMILY_3 = "family_3";

    public function getDependencies()
    {
        return [
                CategoryFixtures::class,
                WordFixtures::class,
        ];
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getLessons() as [$categoryRef, $sequence, $wordStart, $wordEnd, $ref]) {
            $category = $this->getReference($categoryRef);

            $lesson = new Lesson();
            $lesson->setCategory($category);
            $lesson->setSequence($sequence);

            for ($i = $wordStart; $i < $wordEnd; ++$i) {
                $word = $this->getReference("word_$i");
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
            // $lesson = [$categoryRef, $sequence, $wordStart, $wordEnd, $ref];
            [CategoryFixtures::CAT_REF_FAMILY, 0, 0, 10, self::LESSON_REF_FAMILY_1],
            [CategoryFixtures::CAT_REF_FAMILY, 1, 10, 20, self::LESSON_REF_FAMILY_2],
            [CategoryFixtures::CAT_REF_FAMILY, 2, 20, 29, self::LESSON_REF_FAMILY_3],
        ];
    }


    public static function getGroups(): array
    {
        return ['production'];
    }
}
