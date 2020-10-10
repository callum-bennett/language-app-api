<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LessonFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies() {
        return array(
                CategoryFixtures::class,
                WordFixtures::class,
        );
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getLessons() as [$categoryRef, $sequence, $wordStart, $wordEnd])
        {
            $category = $this->getReference($categoryRef);

            $lesson = new Lesson();
            $lesson->setCategory($category);
            $lesson->setSequence($sequence);

            for ($i = $wordStart; $i < $wordEnd; $i++) {
                $word = $this->getReference("word_$i");
                $lesson->addWord($word);
            }

            $objectManager->persist($lesson);
        }

        $objectManager->flush();
    }

    private function getLessons(): array
    {
        return [
            // $lesson = [$categoryRef, $sequence, $wordStart, $wordEnd];
            [CategoryFixtures::CAT_REF_FAMILY, 0, 0, 10],
        ];
    }
}
