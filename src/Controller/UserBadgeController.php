<?php

namespace App\Controller;

use App\Entity\UserBadge;
use App\Repository\UserBadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BadgeController.
 *
 * @Route("/api/v1/user_badge", name="api_v1_user_badge_")
 */
class UserBadgeController extends ApiController
{
    private $em;
    /**
     * @var UserBadgeRepository
     */
    private $repository;
    private $serializer;

    /**
     * UserBadgeController constructor.
     *
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(UserBadge::class);
        $this->serializer = $serializer;
    }


    /**
     * @Route("/", name="get_user_badges")
     */
    public function index()
    {
        $objectToId = function ($o) {
            return $o ? $o->getId() : null;
        };

        try {
            $userBadges = $this->repository->findBy(['user' => $this->getUser()]);
            $data = $this->serializer->serialize($userBadges, 'json', [
                AbstractNormalizer::CALLBACKS => [
                        'user' => $objectToId,
                        'badge' => $objectToId
                ],
            ]);

            return $this->success($data);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
