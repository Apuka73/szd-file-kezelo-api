<?php

namespace Config;

use Phalcon\Di\Di;

class Router extends \Phalcon\Mvc\Router
{
    public function __construct(bool $defaultRoutes = true)
    {
        parent::__construct(false);

        $this->notFound([
                'controller' => 'Controller\\Index',
                'action' => 'notFound']
        );

        $this->add('/', 'Controller\\Index::index');
//        $this->add('/sample/content/list', 'Controller\\Sample\\Content::list');
//        $this->add('/sample/content/get', 'Controller\\Sample\\Content::get');
//        $this->add('/sample/content/save', 'Controller\\Sample\\Content::save');
//        $this->add('/sample/content/delete', 'Controller\\Sample\\Content::delete');

        $this->add('/files', 'Controller\\Api::upload');
        $this->add('/files/{keyId}', 'Controller\\Api::upload');
        $this->add('/blob/{file:[a-zA-Z0-9\/\.]+}', 'Controller\\Api::getFile');
        $this->add('/file', 'Controller\\Api::getFileInfo');
        $this->add('/preflight', 'Controller\\Api::preflight');

    }
}
