<?php

namespace Config;

class Router extends \Phalcon\Mvc\Router
{
    public function __construct(bool $defaultRoutes = true)
    {
        parent::__construct(false);

        $this->notFound([
            'controller' => 'Controller\\Index',
            'action' => 'notFound']
        );

        $this->add('/probe/liveness', 'Controller\\Probe::liveness');

        $this->add('/', 'Controller\\Index::index');


        $this->add('/sample/content/list', 'Controller\\Sample\\Content::list');
        $this->add('/sample/content/get', 'Controller\\Sample\\Content::get');
        $this->add('/sample/content/save', 'Controller\\Sample\\Content::save');
        $this->add('/sample/content/delete', 'Controller\\Sample\\Content::delete');
    }
}
