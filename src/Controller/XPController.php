<?php

namespace App\Controller;

use App\Service\XPService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class XPController.
 *
 * @Route("/api/v1/xp", name="api_v1_xp_")
 */
class XPController extends ApiController
{
    private $serializer;

    /**
     * XPController constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $type
     * @param XPService $XPService
     * @return JsonResponse
     * @Route("/leaderboard/{type}", name="get_leaderboard", methods={"GET"})
     */
    public function leaderboard($type, XPService $XPService)
    {
        try {
            $user = $this->getUser();
            $data = $XPService->getLeaderboard($type, $user);
            return $this->success($this->serializer->serialize($data, "json"));
        } catch (\Exception $e) {
            return $this->success(false);
        }
    }
}
