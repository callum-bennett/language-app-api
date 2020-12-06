<?php

namespace App\Service\Cloud;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class GoogleCloudClient
{

    /**
     * @param ParameterBagInterface $params
     */
    protected static function setCredentials(ParameterBagInterface $params)
    {
        $credentialsPath = $params->get("googleCloudCredentials");
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . $credentialsPath);
    }
}
