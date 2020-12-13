<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use App\Repository\BadgeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BadgeFixtures extends Fixture implements FixtureInterface, FixtureGroupInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function getBadges(): array
    {
        $gcsRoot = "https://storage.googleapis.com";
        $bucket = $this->container->getParameter("googleCloudBucket");
        $imageDir = $this->container->getParameter("googleCloudImageDir");
        $basePath = "$gcsRoot/$bucket/$imageDir";

        return [
            // $badge = [$name, $description, $shortname, $icon, $iconHidden, $notifier];
            [
                    '10 words',
                    'Learn 10 words',
                    '10_words',
                    "{$basePath}badges/10-words.png",
                    "{$basePath}badges/10-words-hidden.png",
                    BadgeRepository::WORD
            ],
            [
                    '100 words',
                    'Learn 100 words',
                    '100_words',
                    "{$basePath}badges/100-words.png",
                    "{$basePath}badges/100-words-hidden.png",
                    'word',
                    BadgeRepository::WORD
            ],
            [
                    '1000 words',
                    'Learn 1000 words',
                    '1000_words',
                    "{$basePath}badges/1000-words.png",
                    "{$basePath}badges/1000-words-hidden.png",
                    'word',
                    BadgeRepository::WORD
            ],
            [
                    'Cadet',
                    'Complete a single lesson',
                    'cadet',
                    "{$basePath}badges/cadet.png",
                    "{$basePath}badges/cadet-hidden.png",
                    'lesson',
                    BadgeRepository::LESSON
            ],
            [
                    'No mistakes!',
                    'Complete a lesson component without making any mistakes',
                    'no_mistakes',
                    "{$basePath}badges/no-mistakes.png",
                    "{$basePath}badges/no-mistakes-hidden.png",
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
