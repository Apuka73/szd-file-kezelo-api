<?php

namespace Controller;




class IndexController extends ApiController
{

    public function notFoundAction()
    {
        throw new \Exception\NotFound();
    }

    public function indexAction()
    {

        return [
            'message' => 'hello world'
        ];
    }

}
