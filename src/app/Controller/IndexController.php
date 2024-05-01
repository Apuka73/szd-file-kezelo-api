<?php

namespace Controller;

use Exception\NotFound;
use Exception\Unauthorized;

class IndexController extends ApiController
{
    public function notFoundAction()
    {
        throw new NotFound();
    }

    public function indexAction()
    {
        return [
            'message' => 'hello world'
        ];
    }
}
