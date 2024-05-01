<?php

namespace Config\Environment;

use Config\Environment;

class MySql extends Environment
{
    public function getDsn()
    {
        return $this->getVar('MYSQL_DSN');
    }

    public function getUser()
    {
        return $this->getVar('MYSQL_USER');
    }

    public function getPassword()
    {
        return $this->getVar('MYSQL_PASSWORD');
    }
}