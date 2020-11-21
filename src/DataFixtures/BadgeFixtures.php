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
        foreach ($this->getBadges() as [$name, $description, $shortname, $icon, $notifier]) {
            $badge = new Badge();
            $badge->setName($name);
            $badge->setDescription($description);
            $badge->setShortname($shortname);
            $badge->setIcon($icon);
            $badge->setNotifier($notifier);

            $objectManager->persist($badge);
        }

        $objectManager->flush();
    }

    private function getBadges(): array
    {
        return [
            // $badge = [$name, $description, $shortname, $icon $notifier];
            ['10 words', 'Learn 10 words', '10_words', 'icon', BadgeRepository::WORD],
            ['100 words', 'Learn 100 words', '100_words', 'icon', 'word', BadgeRepository::WORD],
            ['1000 words', 'Learn 1000 words', '1000_words', 'icon', 'word', BadgeRepository::WORD],
            ['Cadet', 'Complete a single lesson', 'cadet', 'icon', 'lesson', BadgeRepository::LESSON],
            ['No mistakes!', 'Complete a lesson component without making any mistakes', 'no_mistakes', 'icon', 'lesson', BadgeRepository::LESSON],
        ];
    }

    public static function getGroups(): array
    {
        return ['production'];
    }
}
