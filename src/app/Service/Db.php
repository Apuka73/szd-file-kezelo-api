<?php

namespace Service;

use Config\Environment;

class Db extends \Phalcon\Db\Adapter\Pdo\Mysql
{
    public function __construct()
    {
        parent::__construct([
            "dsn"     => Environment::get()->getMySql()->getDsn(),
            "username" => Environment::get()->getMySql()->getUser(),
            "password" => Environment::get()->getMySql()->getPassword(),
        ]);
    }
}
