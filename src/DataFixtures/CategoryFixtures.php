<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements FixtureInterface
{
    public const CAT_REF_FAMILY = 'family';
    public const CAT_REF_TOP_100_WORDS = 'top100words';
    public const CAT_REF_TRAVEL = 'travel';
    public const CAT_REF_NUMBERS = 'numbers';

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getCategories() as [$name, $imagePath, $ref]) {
            $category = new Category();
            $category->setName($name);
            $category->setImageUrl($imagePath);

            $objectManager->persist($category);
            $this->addReference($ref, $category);
        }

        $objectManager->flush();
    }

    private function getCategories(): array
    {
        return [
            // $category = [$name, $imagePath, $ref];
            ["Top 100 words", "/static/media/top100.d0695270.jpg", self::CAT_REF_TOP_100_WORDS],
            ["Family", "/static/media/static/media/people.f3302419.jpg", self::CAT_REF_FAMILY],
            ["Travel", "/static/media/top100.d0695270.jpg", self::CAT_REF_TRAVEL],
            ["Numbers", "/static/media/top100.d0695270.jpg", self::CAT_REF_NUMBERS],
        ];
    }
}
