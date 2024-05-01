<?php

namespace Controller;


use Phalcon\Mvc\Controller;

class ProbeController extends Controller
{
	public function livenessAction()
    {
        // test db connection
        $this->db->query('SHOW TABLES')->fetchAll();

        return true;
    }
    
}