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
        $fixtureImageDir = realpath(__DIR__) . "/images/";

        $destinationDir = $this->container->getParameter("publicImageDir");

        foreach ($this->getCategories() as [$name, $fileName, $ref])
        {
            $originalFilePath = $fixtureImageDir.$fileName;
            $copiedFileName = "copy-$fileName";
            $copiedFilePath = $fixtureImageDir.$copiedFileName;
            copy($originalFilePath, $copiedFilePath);

            $file = new UploadedFile($copiedFilePath, str_replace(" ", "", $name), null, null, true);
            $fileName = $this->fileUploader->upload($file, $destinationDir);

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
            // $category = [$name, $fileName, $ref];
            ["Top 100 words", "top100.jpg", self::CAT_REF_TOP_100_WORDS],
            ["Family", "people.jpg", self::CAT_REF_FAMILY],
            ["Travel", "travel.jpg", self::CAT_REF_TRAVEL],
            ["Numbers", "numbers.jpg", self::CAT_REF_NUMBERS],
        ];
    }
}
