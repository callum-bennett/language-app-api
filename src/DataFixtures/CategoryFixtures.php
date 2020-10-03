<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Service\FileUploader;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryFixtures extends Fixture implements FixtureInterface, ContainerAwareInterface
{
    public const CAT_REF_FAMILY = 'family';
    public const CAT_REF_TOP_100_WORDS = 'top100words';
    public const CAT_REF_TRAVEL = 'travel';
    public const CAT_REF_NUMBERS = 'numbers';

    /**
     * @var ContainerInterface
     */
    private $container;
    private $fileUploader;

    /**
     * CategoryFixtures constructor.
     *
     * @param FileUploader $fileUploader
     */
    public function __construct(FileUploader $fileUploader) {
        $this->fileUploader = $fileUploader;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getCategories() as [$name, $imagePath, $ref]) {
            $directory = $this->container->getParameter("publicImageDir");
            $file = new UploadedFile($imagePath, str_replace(" ", "", $name), null, null, true);
            $fileName = $this->fileUploader->upload($file, $directory);

            $category = new Category();
            $category->setName($name);
            $category->setImageUrl($fileName);

            $objectManager->persist($category);
            $this->addReference($ref, $category);
        }

        $objectManager->flush();
    }

    private function getCategories(): array
    {
        return [
            // $category = [$name, $imagePath, $ref];
            ["Top 100 words", realpath(__DIR__) . "/images/top100.jpg", self::CAT_REF_TOP_100_WORDS],
            ["Family", realpath(__DIR__) . "/images/people.jpg", self::CAT_REF_FAMILY],
            ["Travel", realpath(__DIR__) . "/images/travel.jpg", self::CAT_REF_TRAVEL],
            ["Numbers", realpath(__DIR__) . "/images/numbers.jpg", self::CAT_REF_NUMBERS],
        ];
    }
}
