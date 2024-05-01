<?php

namespace Exception;

class NotFound extends Http
{
    public function __construct($message = '', $statusCode = 400, $context = [])
    {
        parent::__construct('Not found', 404, $context);
    }
}
