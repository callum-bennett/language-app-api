<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BadgeController.
 *
 * @Route("/api/v1/badge", name="api_v1_badge_")
 */
class BadgeController extends ApiController
{
    private $em;
    /**
     * @var BadgeRepository
     */
    private $repository;
    private $serializer;

    /**
     * BadgeController constructor.
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Badge::class);
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="get_badges", methods={"GET"})
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $badges = $this->repository->findAll();
            $data = $this->serializer->serialize($badges, 'json', [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => ['userBadges'],
            ]);
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
