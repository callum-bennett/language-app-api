<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements FixtureInterface, FixtureGroupInterface
{
    public const CAT_REF_FAMILY = 'family';
    public const CAT_REF_TOP_100_WORDS = 'top100words';
    public const CAT_REF_TRAVEL = 'travel';
    public const CAT_REF_NUMBERS = 'numbers';

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getCategories() as [$name, $imageUrl, $ref]) {
            $category = new Category();
            $category->setName($name);
            $category->setImageUrl($imageUrl);

            $objectManager->persist($category);
            $this->addReference($ref, $category);
        }

        $objectManager->flush();
    }

    private function getCategories(): array
    {
        return [
            // $category = [$name, $imageUrl, $ref];
            //['Top 100 words', 'https://i.ibb.co/3rT8sq8/top100.jpg', self::CAT_REF_TOP_100_WORDS],
            ['Family', 'https://i.ibb.co/t87Pv0P/family.jpg', self::CAT_REF_FAMILY],
            ['Travel', 'https://i.ibb.co/4WHcR59/travel.jpg', self::CAT_REF_TRAVEL],
            ['Numbers', 'https://i.ibb.co/HtzjxcF/numbers.jpg', self::CAT_REF_NUMBERS],
        ];
    }

    public static function getGroups(): array
    {
        return ['production'];
    }
}
