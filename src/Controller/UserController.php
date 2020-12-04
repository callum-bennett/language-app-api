<?php

namespace App\Controller;

use App\Service\NotificationService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 *
 * @Route("/api/v1/user", name="api_v1_user_")
 */
class UserController extends ApiController
{
    private $em;

    private $notificationService;

    /**
     * UserController constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @Route("/clear_notifications", name="clear_notifications", methods={"POST"})
     * @param Request $request
     */
    public function clear_notifications(Request $request)
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
