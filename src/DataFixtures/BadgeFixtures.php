<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use App\Repository\BadgeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BadgeFixtures extends Fixture implements FixtureInterface, FixtureGroupInterface
{

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getBadges() as [$name, $description, $shortname, $icon, $iconHidden, $notifier]) {
            $badge = new Badge();
            $badge->setName($name);
            $badge->setDescription($description);
            $badge->setShortname($shortname);
            $badge->setIcon($icon);
            $badge->setIconHidden($iconHidden);
            $badge->setNotifier($notifier);

            $objectManager->persist($badge);
        }

        $objectManager->flush();
    }

    private function getBadges(): array
    {
        return [
            // $badge = [$name, $description, $shortname, $icon, $iconHidden, $notifier];
            [
                    '10 words',
                    'Learn 10 words',
                    '10_words',
                    'https://i.ibb.co/9nPXt3X/10-words.png',
                    'https://i.ibb.co/3mYYMLc/10-words-hidden.png',
                    BadgeRepository::WORD
            ],
            [
                    '100 words',
                    'Learn 100 words',
                    '100_words',
                    'https://i.ibb.co/1X79Wdr/100-words.png',
                    'https://i.ibb.co/0Kyr6bj/100-words-hidden.png',
                    'word',
                    BadgeRepository::WORD
            ],
            [
                    '1000 words',
                    'Learn 1000 words',
                    '1000_words',
                    'https://i.ibb.co/DrLTybp/1000-words.png',
                    'https://i.ibb.co/FXWqMdW/1000-words-hidden.png',
                    'word',
                    BadgeRepository::WORD
            ],
            [
                    'Cadet',
                    'Complete a single lesson',
                    'cadet',
                    'https://i.ibb.co/GJchVGF/cadet.png',
                    'https://i.ibb.co/J3Nj542/cadet-hidden.png',
                    'lesson',
                    BadgeRepository::LESSON
            ],
            [
                    'No mistakes!',
                    'Complete a lesson component without making any mistakes',
                    'no_mistakes',
                    'https://i.ibb.co/J2VMNzt/no-mistakes.png',
                    'https://i.ibb.co/jHRnb29/no-mistakes-hidden.png',
                    'lesson',
                    BadgeRepository::LESSON
            ],
        ];
    }

    public static function getGroups(): array
    {
        return ['production'];
    }
}
