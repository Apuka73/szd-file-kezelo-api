<?php

namespace Exception;

class NotFound extends Http
{
    public function __construct($message = '', $statusCode = 400, $context = [])
    {
        $request = \Application::getApp()->request;
        parent::__construct('Not found [' . $request->getMethod() . ' - ' . $request->getURI() . ' - ]', 404, $context);
    }
}
