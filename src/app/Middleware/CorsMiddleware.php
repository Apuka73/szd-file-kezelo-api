<?php

namespace Middleware;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;

class CorsMiddleware extends Injectable
{
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $response = $this->response;

        // Beállítja a szükséges CORS fejléceket
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');


        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            $response->setHeader('Access-Control-Allow-Headers', 'Origin, Tus-Resumable, Tus-Version, Location, Upload-Length, Upload-Offset, Upload-Metadata, Tus-Max-Size, Tus-Extension, Tus-Resumable, Upload-Defer-Length, X-HTTP-Method-Override, Content-Type');
            $response->setHeader('Access-Control-Expose-Headers', 'Tus-Resumable, Tus-Version, Location, Upload-Length, Upload-Offset, Upload-Metadata, Tus-Max-Size, Tus-Extension, Content-Type, Stream-Media-ID');
        }
        // Ha az HTTP metódus OPTIONS, ne folytassa a kérés feldolgozását
        if ($this->request->getMethod() == 'OPTIONS') {
            $response->setStatusCode(200, 'OK');
            $response->setHeader('Access-Control-Allow-Origin', '*');
            $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
            $response->setHeader('Access-Control-Allow-Headers', 'Authorization');
        }

        return true;
    }
}
