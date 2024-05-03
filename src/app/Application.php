<?php

use Config\Router;
use Exception\Http;
use Middleware\CorsMiddleware;
use Model\AbstractModel;
use Phalcon\Di\Di;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Url;
use Service\Filter\FilterFactory;
use Service\Logger;
use Phalcon\Mvc\Dispatcher;

class Application extends \Phalcon\Mvc\Application
{
    public function __construct()
    {
//        set_error_handler( array($this, 'errorHandler'), E_ALL);
//        set_exception_handler( array($this, 'exceptionHandler') );


        $this->useImplicitView(false);
        $this->di = new \Phalcon\Di\FactoryDefault();
        Di::setDefault($this->di);

        $this->di->setShared('router', Router::class);
        $this->di->setShared('filter', function () {
            $factory = new FilterFactory();
            return $factory->newInstance();
        });
        $this->di->setShared('db', \Service\Db::class);
        $this->di->getShared('db')->connect();
        $this->di->setShared('storage', \Service\Storage::class);

        AbstractModel::setup([
            'notNullValidations' => false
        ]);

        $eventsManager = new EventsManager();
        $eventsManager->attach(
            'dispatch:beforeExecuteRoute',
            new CorsMiddleware()
        );
        $dispatcher = new Dispatcher();
        $dispatcher->setEventsManager($eventsManager);
        $this->di->setShared('dispatcher', $dispatcher);

        parent::__construct($this->di);


    }

    public function exceptionHandler(\Throwable $exception)
    {
        if ($exception instanceof Http) {
            $this->response->setJsonContent([
                'error' => $exception->getMessage()
            ]);
            $this->response->setStatusCode($exception->getStatusCode());
            $this->response->send();

            Logger::warning($exception->getMessage(), [
                'context' => $exception->getContext(),
                'trace' => $exception->getTrace(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return true;
        }

        Logger::error($exception->getMessage(), [
            'trace' => $exception->getTrace(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        $this->response->setStatusCode(500);
        $this->response->send();
    }

    public function errorHandler(int $errno , string $errstr, string $errfile, int $errline , $errcontext = [])
    {
        $errfile = $errfile ?? "unknown file";
        $errstr  = $errstr ?? "shutdown";
        $errno   = $errno ?? E_CORE_ERROR;
        $errline = $errline ?? 0;
        $errcontext = $errcontext ?? [];

        switch ($errno) {
            case E_CORE_ERROR :
            case E_ERROR :
            case E_NOTICE :
            case E_WARNING :
            case E_DEPRECATED :
            case E_STRICT : $type = 'error';
                break;
            default : $type = 'info';
        }

        Logger::{$type}($errstr, [
            $errfile,
            $errstr,
            $errno,
            $errline,
            $errcontext,
        ]);

        throw new \Exception($errstr);
    }
}
