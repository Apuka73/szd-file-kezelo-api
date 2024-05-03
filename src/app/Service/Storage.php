<?php

namespace Service;

use Aws\S3\S3Client;
use Config\Environment;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Config;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;

class Storage
{
    private $type;
    private $bucket;

    public function __construct()
    {
        $this->type = Environment::get()->getFileStorage()->getType();

    }

    public function getType()
    {
        return $this->type;
    }

    public function getBucket(): Filesystem
    {
        if ($this->bucket instanceof Filesystem) {
            return $this->bucket;
        }

        try {
            switch ($this->type) {
                case 'local':
                    $basePath = Environment::get()->getBasePath() . '/' . Environment::get()->getFileStorage()->getLocalPath();
                    file_exists($basePath) || mkdir($basePath, 0777, true);
                    $adapter = new LocalFilesystemAdapter($basePath);
                    $this->bucket = new Filesystem($adapter,
                        publicUrlGenerator: new class() implements PublicUrlGenerator {
                            public function publicUrl(string $path, Config $config): string
                            {
                                $env = Environment::get()->getFileStorage();
                                return $env->getLocalUrl() . '/' . $env->getLocalPath() . '/' . $path;
                            }
                        }
                    );
                    break;
                case 's3':
                    $client = new S3Client([
                        'credentials' => [
                            'key' => Environment::get()->getFileStorage()->getS3KeyId(),
                            'secret' => Environment::get()->getFileStorage()->getS3KeySecret(),
                        ],
                        'region' => Environment::get()->getFileStorage()->getS3Region(),
                        'version' => 'latest',
                        'endpoint' => Environment::get()->getFileStorage()->getS3Endpoint(),
                        'bucket_endpoint' => true,
                        'use_path_style_endpoint' => false,
                    ]);
                    $adapter = new AwsS3V3Adapter($client, Environment::get()->getFileStorage()->getS3BucketName());
                    $this->bucket = new Filesystem($adapter);


                    break;

                default:
                    throw new \Exception('Unknown storage type');
            }


        } catch (\Exception $e) {
            throw new \Exception('Error creating bucket: ' . $e->getMessage());
        }

        return $this->bucket;
    }

}