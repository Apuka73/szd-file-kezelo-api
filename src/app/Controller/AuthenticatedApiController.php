<?php

namespace Controller;

use Exception\Unauthorized;

class AuthenticatedApiController extends ApiController
{
    public function beforeExecuteRoute()
    {
        parent::beforeExecuteRoute();

        $userLoggedIn = isset($_GET['logged']);

        if (!$userLoggedIn) {
            throw new Unauthorized();
        }

    }
}
