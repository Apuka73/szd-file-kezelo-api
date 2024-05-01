<?php

namespace Exception;

class Http extends \Exception
{
    protected $context;
    protected $statusCode;

    public function __construct($message = '', $statusCode = 400, $context = [])
    {
        $this->context = $context;
        $this->statusCode = $statusCode;
        parent::__construct($message);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getStatusCode(): mixed
    {
        return $this->statusCode;
    }
}
