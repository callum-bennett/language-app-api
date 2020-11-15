<?php

namespace App\DataFixtures;

use App\Entity\LessonComponent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LessonComponentFixtures extends Fixture implements FixtureGroupInterface
{
    public const COMPONENT_REF_SLIDES = "slides";
    public const COMPONENT_REF_MULTIPLE_CHOICE = "multiple_choice";
    public const COMPONENT_REF_CROSSWORD = "crossword";

    public function getDependencies()
    {
        return [
                CategoryFixtures::class,
                WordFixtures::class,
        ];
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getLessonComponents() as [$name, $ref]) {

            $lessonComponent = new LessonComponent();
            $lessonComponent->setName($name);
            $this->addReference($ref, $lessonComponent);

            $objectManager->persist($lessonComponent);
        }

        $objectManager->flush();
    }

    private function getLessonComponents(): array
    {
        return [
            // $component = [$name, $ref];
            ["Slides", self::COMPONENT_REF_SLIDES],
            ["Multiple Choice", self::COMPONENT_REF_MULTIPLE_CHOICE],
            ["Crossword", self::COMPONENT_REF_CROSSWORD],
        ];
    }


    public static function getGroups(): array
    {
        return ['production'];
    }
}
