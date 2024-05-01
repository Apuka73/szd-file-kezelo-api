<?php

include __DIR__ . "/../vendor/autoload.php";
include __DIR__ . "/../app/autoload.php";

$phalcon = new Application();
$phalcon->handle($_SERVER['REQUEST_URI'])->send();
