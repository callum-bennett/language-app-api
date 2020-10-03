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
        //$fixtureImageDir = realpath(__DIR__) . "/images/";
        //$destinationDir = $this->container->getParameter("publicImageDir");

        foreach ($this->getCategories() as [$name, $imageUrl, $ref])
        {
            //$originalFilePath = $fixtureImageDir.$fileName;
            //$copiedFileName = "copy-$fileName";
            //$copiedFilePath = $fixtureImageDir.$copiedFileName;
            //copy($originalFilePath, $copiedFilePath);
            //
            //$file = new UploadedFile($copiedFilePath, str_replace(" ", "", $name), null, null, true);
            //$fileName = $this->fileUploader->upload($file, $destinationDir);

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
            ["Top 100 words", "https://i.ibb.co/3rT8sq8/top100.jpg", self::CAT_REF_TOP_100_WORDS],
            ["Family", "https://i.ibb.co/t87Pv0P/family.jpg", self::CAT_REF_FAMILY],
            ["Travel", "https://i.ibb.co/4WHcR59/travel.jpg", self::CAT_REF_TRAVEL],
            ["Numbers", "https://i.ibb.co/HtzjxcF/numbers.jpg", self::CAT_REF_NUMBERS],
        ];
    }
}
