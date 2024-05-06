<?php

namespace Controller;

use Exception\BadRequest;
use League\Flysystem\Filesystem;
use Middleware\CorsMiddleware;
use Model\File;
use Phalcon\Di;
use Phalcon\Http\Request\FileInterface;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Predis\Command\Redis\DISCARD;
use Service\Logger;
use TusPhp\Cache\ApcuStore;
use TusPhp\Tus\Server;
use Phalcon\Encryption\Security\Random;

class ApiController extends Controller
{
    public function beforeExecuteRoute()
    {

//        if (isset($_SERVER['HTTP_ORIGIN'])) {
//            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
//            header('Access-Control-Allow-Credentials: true');
//            header('Access-Control-Max-Age: 86400');
//            exit(0);
//        }
//        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
//                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//                header('Access-Control-Allow-Origin: *');
//                header('Access-Control-Allow-Headers: Origin, Tus-Resumable, Tus-Version, Location, Upload-Length, Upload-Offset, Upload-Metadata, Tus-Max-Size, Tus-Extension, Tus-Resumable, Upload-Defer-Length, X-HTTP-Method-Override, Content-Type');
//                header('Access-Control-Expose-Headers: Tus-Resumable, Tus-Version, Location, Upload-Length, Upload-Offset, Upload-Metadata, Tus-Max-Size, Tus-Extension, Content-Type, Stream-Media-ID');
//            }
//
//            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
//                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
//            }
//
//        }
    }

    public function afterExecuteRoute()
    {
        $headers = $this->response->getHeaders();
        if (!$headers->has('Content-Type')) {
            $this->response->setJsonContent($this->dispatcher->getReturnedValue());
        }
    }

    public function uploadAction($keyId = null)
    {

        try {

            $server = new Server('file');
            $server->setCache(
                new \TusPhp\Cache\FileStore(sys_get_temp_dir() . '/')
            );
            $server->setUploadDir(sys_get_temp_dir() . '/');
            $server->event()->addListener('tus-server.upload.created', function (\TusPhp\Events\TusEvent $event) {
//                Logger::info('Feltöltés létrehozva: ' . $event->getFile()->getFilePath());
            });
            $server->event()->addListener('tus-server.upload.progress', function (\TusPhp\Events\TusEvent $event) {
//                Logger::info('Feltöltés folyamatban: ', $event->getFile()->getFilePath());
            });

            $server->event()->addListener('tus-server.upload.complete', function (\TusPhp\Events\TusEvent $event) {
                $uploadedFile = $event->getFile();
                $filePath = $uploadedFile->getFilePath();
                $fileSize = $uploadedFile->getFileSize();

                $file = new \Phalcon\Http\Request\File([
                    'name' => basename($filePath),
                    'type' => mime_content_type($filePath),
                    'tmp_name' => $filePath,
                    'error' => 0,
                    'size' => $fileSize,
                ]);
                $resp = $this->moveToStorage($file);
                $this->response->setJsonContent($resp)->send();
                exit(0);
            });
            $response = $server->serve();
            $response->send();
            exit(0);

        } catch (\Exception $e) {
            Logger::error('TUS error', $e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function moveToStorage(FileInterface $file): array
    {
        $source = sys_get_temp_dir() . '/' . $file->getName();
        if (!file_exists($source)) {
            throw new \Exception\File('Uploaded file not found. [' . $file->getName() . ']');
        }
        $random = new Random();
        $helper = new \Phalcon\Support\HelperFactory();
        $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
        do {
            $storePath = $helper->lower($random->base62(2) . '/' . $random->base62(2) . '/' . $random->base62() . '.' . $extension);
        } while (File::findFirstByPath($storePath) !== null);
        /** @var Filesystem $bucket */
        try {
            $bucket = $this->storage->getBucket();
            $fileContent = file_get_contents($source);
            $bucket->write('/' . $storePath, $fileContent);
        } catch (\Exception $e) {
            throw new \Exception\File('File copy to bucket error. [' . $e->getMessage() . ']');
        }
        $file = new File(
            [
                'name' => $file->getName(),
                'path' => $storePath
            ]
        );
        if (!$file->save()) {
            throw new \Exception\File('File save to db error. [' . $source . ']');
        }

        return ['id' => $file->getId(), 'url' => $file->getPublicUrl()];

    }

    public function getFileAction($file)
    {
        $file = File::findFirstByPath($file);
        if ($file === null) {
            throw new BadRequest('File not found. [' . $file . ']');
        }
        $bucket = $this->storage->getBucket();
        $content = $bucket->read($file->getPath());
        $tempPath = tempnam(sys_get_temp_dir(), 'download');
        file_put_contents($tempPath, $content);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tempPath);
        finfo_close($finfo);
        $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setFileToSend($tempPath, $file->getName());
        return $this->response;
    }

    public function getFileInfoAction()
    {
        $ids = $this->request->get('ids', null, '');
        if (empty($ids)) {
            throw new BadRequest('Empty ids');
        }
        $idsArray = explode(',', $ids);
        $placeholders = array_fill(0, count($idsArray), '?');
        $binds = [];
        foreach ($idsArray as $index => $id) {
            $placeholders[$index] = ":id{$index}:";
            $binds["id{$index}"] = $id;
        }
        $files = File::find([
            'conditions' => "id IN (" . implode(',', $placeholders) . ")",
            'bind' => $binds
        ]);

        $resp = [];
        foreach ($files as $file) {
            $resp[] = $file->toResponseArray();
        }

        return $resp;
    }

    public function preflightAction()
    {
        $content_type = 'application/json';
        $status = 200;
        $description = 'OK';
        $response = $this->response;

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $description;
        $response->setRawHeader($status_header);
        $response->setStatusCode($status, $description);
        $response->setContentType($content_type, 'UTF-8');
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setHeader('Access-Control-Allow-Headers', 'Authorization');
        $response->setHeader('Content-type', $content_type);
        $response->sendHeaders();
    }

}
