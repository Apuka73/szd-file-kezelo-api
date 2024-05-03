<?php

namespace Config\Environment;

use Config\Environment;

class FileStorage extends Environment
{
    public function getType(): string
    {
        return $this->getVar('STORAGE_TYPE');
    }

    public function getLocalPath(): string
    {
        return $this->getVar('STORAGE_LOCAL_PATH');
    }

    public function getLocalUrl()
    {
        return $this->getVar('STORAGE_LOCAL_URL');
    }

    public function getS3KeyId(): string
    {
        return $this->getVar('STORAGE_S3_KEY_ID');
    }

    public function getS3KeySecret(): string
    {
        return $this->getVar('STORAGE_S3_KEY_SECRET');
    }

    public function getS3Region(): string
    {
        return $this->getVar('STORAGE_S3_REGION');
    }

    public function getS3Endpoint(): string
    {
        return $this->getVar('STORAGE_S3_ENDPOINT');
    }

    public function getS3BucketId(): string
    {
        return $this->getVar('STORAGE_S3_BUCKET_ID');
    }

    public function getS3BucketName(): string
    {
        return $this->getVar('STORAGE_S3_BUCKET_NAME');
    }
}
