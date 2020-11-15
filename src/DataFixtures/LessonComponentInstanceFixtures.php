<?php

namespace App\DataFixtures;

use App\Entity\LessonComponentInstance;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LessonComponentInstanceFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function getDependencies()
    {
        return [
                LessonFixtures::class,
                LessonComponentFixtures::class,
        ];
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getLessonComponentInstances() as [$lessonComponentRef, $lessonRef, $sequence]) {
            $lesson = $this->getReference($lessonRef);
            $lessonComponent = $this->getReference($lessonComponentRef);

            $componentInstance = new LessonComponentInstance();
            $componentInstance->setLesson($lesson);
            $componentInstance->setLessonComponent($lessonComponent);
            $componentInstance->setSequence($sequence);

            $objectManager->persist($componentInstance);
        }

        $objectManager->flush();
    }

    private function getLessonComponentInstances(): array
    {
        return [
            // $lesson = [$lessonComponentRef, $lessonRef, $sequence];
            [LessonComponentFixtures::COMPONENT_REF_SLIDES, LessonFixtures::LESSON_REF_FAMILY_1, 0],
            [LessonComponentFixtures::COMPONENT_REF_MULTIPLE_CHOICE, LessonFixtures::LESSON_REF_FAMILY_1, 1],
            [LessonComponentFixtures::COMPONENT_REF_CROSSWORD, LessonFixtures::LESSON_REF_FAMILY_1, 2],
            [LessonComponentFixtures::COMPONENT_REF_SLIDES, LessonFixtures::LESSON_REF_FAMILY_2, 0],
            [LessonComponentFixtures::COMPONENT_REF_MULTIPLE_CHOICE, LessonFixtures::LESSON_REF_FAMILY_2, 1],
            [LessonComponentFixtures::COMPONENT_REF_SLIDES, LessonFixtures::LESSON_REF_FAMILY_3, 0],
            [LessonComponentFixtures::COMPONENT_REF_MULTIPLE_CHOICE, LessonFixtures::LESSON_REF_FAMILY_3, 1],
        ];
    }


    public static function getGroups(): array
    {
        return ['production'];
    }
}
