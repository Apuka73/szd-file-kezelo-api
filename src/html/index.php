<?php


//ini_set('display_errors', 1);
//error_reporting(E_ALL);

include __DIR__ . "/../vendor/autoload.php";
include __DIR__ . "/../app/autoload.php";

$phalcon = new Application();
//\Service\Logger::info('Request',Application::getApp()->request->getURI());
$phalcon->handle($_SERVER['REQUEST_URI'])->send();
