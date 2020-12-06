<?php

namespace App\Service\Cloud;

use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GCSUploader extends GoogleCloudClient
{
    private $client;
    private $params;

    public static $gcsBasePath = "https://storage.googleapis.com/";

    /**
     * GCSUploader constructor.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        self::setCredentials($params);
        $this->params= $params;
        $this->client = new StorageClient();
    }

    /**
     * @param string $gcsUri
     * @return string|string[]
     */
    private function gcsToUri(string $gcsUri)
    {
        return str_replace("gs://", self::$gcsBasePath, $gcsUri);
    }

    /**
     * @param $file
     * @param $directory
     * @return false|StorageObject
     */
    public function upload($file, $directory)
    {
        $bucketName = $this->params->get("googleCloudBucket");

        try {
            $bucket = $this->client->bucket($bucketName);
            $storageObject = $bucket->upload($file, [
                    'name' => ltrim($directory, "/")
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return $this->gcsToUri($storageObject->gcsUri());
    }
}
