<?php

if (empty(getenv('MYSQL_DSN'))) {
    echo "\e[33m[WARN]\e[39m Skip phinx migrate because 'MYSQL_DSN' env not found\n";
    exit;
}


const MIGRATION_PATH = '/var/www/mysql-migrations';

if (!file_exists(MIGRATION_PATH)) {
    $path = MIGRATION_PATH;
    `mkdir -p $path`;
    `chmod 777 -R $path`;
}


// Get PDO object
$pdo = new PDO(
    'mysql:' . getenv('MYSQL_DSN'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'),
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_unicode_ci',
    )
);

return [
    'foreign_keys' => false,
    "paths" => [
        "migrations" => MIGRATION_PATH
    ],
    'environments' => [
        'default_environment' => 'local',
        'local' => [
            'name' => getenv('MYSQL_DB'),
            'connection' => $pdo,
        ]
    ]
];
