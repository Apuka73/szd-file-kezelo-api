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
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, HEAD, DELETE');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');

        // Ha az HTTP metódus OPTIONS (pre-flight kérés), ne folytassa a kérés feldolgozását
        if ($this->request->getMethod() == 'OPTIONS') {
            $response->setStatusCode(200, 'OK');
            return false;
        }

        return true;
    }
}