<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Exception\IOException;

class FileUploader
{

    private $fileSystem;

    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function upload(UploadedFile $file, $directory)
    {
        $newFilename = false;

        if ($file && $directory) {
            try {
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $filename.'-'.uniqid().'.'.$file->getExtension();
                $file->move($directory, $newFilename);
            } catch (FileException $e) {
                return false;
            }
        }

        return $newFilename;
    }
}
