<?php

namespace Config;

use Config\Environment\MySql;
use Config\Environment\SendCloud;

class Environment
{
    protected static $instances;
    protected $vars = [];

    public function __construct()
    {
        $this->vars = \array_merge($_ENV, $_SERVER);
    }

    public static function get()
    {
        if (!isset(self::$instances['self'])) {
            self::$instances['self'] = new self();
        }

        return self::$instances['self'];
    }

    protected function getVar(string $name)
    {
        if (empty($this->vars[$name])) {
            throw new \Exception('Env var value is missing [' . $name . ']');
        }

        return $this->vars[$name];
    }

    protected function getInstance(string $class)
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

    public function getMySql():MySql
    {
        return self::getInstance(MySql::class);
    }
}
