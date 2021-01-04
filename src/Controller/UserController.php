<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UserController.
 *
 * @Route("/api/v1/user", name="api_v1_user_")
 */
class UserController extends ApiController
{
    private $em;

    private $notificationService;

    private $serializer;

    private $repo;

    /**
     * UserController constructor.
     *
     * @param NotificationService $notificationService
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(NotificationService $notificationService, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->notificationService = $notificationService;
        $this->em = $em;
        $this->serializer = $serializer;

        $this->repo = $this->em->getRepository(User::class);
    }

    /**
     * @Route("/get_config", name="get_config", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function get_config(Request $request): JsonResponse
    {
        $user = $this->getUser();
        try {
            return $this->success($this->serializer->serialize([
                    "onboarded" => $user->getOnboarded()
            ], 'json'));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @Route("/set_onboarded", name="set_onboarded", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function set_onboarded(Request $request): JsonResponse
    {
        $user = $this->getUser();
        try {
            $user->setOnboarded(true);
            $this->em->persist($user);
            $this->em->flush();

            return $this->success(true);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @Route("/clear_notifications", name="clear_notifications", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function clear_notifications(Request $request): JsonResponse
    {
        $user = $this->getUser();
        try {
            $data = json_decode($request->getContent());
            return $this->success($this->notificationService->clearNotificationsOfType($user, $data->type));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
