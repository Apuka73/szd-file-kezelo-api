<?php

namespace Exception;

class Unauthorized extends Http
{
    public function __construct()
    {
        parent::__construct('Unauthorized', 401);
    }
}
