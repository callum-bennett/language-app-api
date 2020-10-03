<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PublicController
 * @package App\Controller
 */
class PublicController extends AbstractController
{
    /**
     * @Route("/image/{filename}", name="image")
     * @param $filename
     * @return Response
     */
    public function image($filename): Response
    {
        return new BinaryFileResponse("images/{$filename}");
    }
}
