<?php


ini_set('display_errors', 1);
error_reporting(E_ALL);

include __DIR__ . "/../vendor/autoload.php";
include __DIR__ . "/../app/autoload.php";

$phalcon = new Application();

$phalcon->handle($_SERVER['REQUEST_URI'])->send();
