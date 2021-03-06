<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController.
 *
 * @Route("/api/v1/", name="api_v1_")
 */
class ApiController extends AbstractController
{
    protected const STANDARD_ERROR = "An error has occurred";

    /**
     * @param $payload
     * @return array
     */
    private static function build($payload)
    {
        return [
                'payload' => $payload,
                'time' => time()
        ];
    }

    /**
     * @param $payload
     * @return JsonResponse
     */
    protected function success($payload)
    {
        return new JsonResponse(self::build($payload), 200);
    }

    /**
     * @param string $message
     * @param int $errorCode
     * @return JsonResponse
     */
    protected function error($message = self::STANDARD_ERROR, $errorCode = 500)
    {
        if ($this->getParameter("app.env") === "prod") {
            $message = self::STANDARD_ERROR;
        }

        return new JsonResponse($message, $errorCode);
    }
}
