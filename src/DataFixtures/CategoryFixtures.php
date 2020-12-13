<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryFixtures extends Fixture implements FixtureInterface, FixtureGroupInterface, ContainerAwareInterface
{
    public const CAT_REF_FAMILY = 'family';
    public const CAT_REF_TOP_100_WORDS = 'top100words';
    public const CAT_REF_TRAVEL = 'travel';
    public const CAT_REF_NUMBERS = 'numbers';

    /**
     * @var ContainerInterface
     */
    private $container;

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

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function getCategories(): array
    {
        $gcsRoot = "https://storage.googleapis.com";
        $bucket = $this->container->getParameter("googleCloudBucket");
        $imageDir = $this->container->getParameter("googleCloudImageDir");
        $basePath = "$gcsRoot/$bucket/$imageDir";

        return [
            // $category = [$name, $imageUrl, $ref];
            //['Top 100 words', "{$basePath}categories/top100.jpg", self::CAT_REF_TOP_100_WORDS],
            ['Family', "{$basePath}categories/family.jpg", self::CAT_REF_FAMILY],
            ['Travel', "{$basePath}categories/travel.jpg", self::CAT_REF_TRAVEL],
            ['Numbers', "{$basePath}categories/numbers.jpg", self::CAT_REF_NUMBERS],
        ];
    }

    public static function getGroups(): array
    {
        return ['production'];
    }
}
