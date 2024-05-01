<?php

namespace Service;

class Logger
{
    const SEVERITY_DEBUG = 'debug';
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARN = 'warn';
    const SEVERITY_ERROR = 'error';

    public static function log($message, $severity, $context = [])
    {
        file_put_contents('php://stderr', json_encode([
                'message' => $message,
                'severity' => $severity,
                'context' => $context,
            ]) . "\n");
    }

    public static function info($message, $context = [])
    {
        self::log($message, self::SEVERITY_INFO, $context);
    }

    public static function debug($message, $context = [])
    {
        self::log($message, self::SEVERITY_DEBUG, $context);
    }

    public static function error($message, $context = [])
    {
        self::log($message, self::SEVERITY_ERROR, $context);
    }

    public static function warning($message, $context = [])
    {
        self::log($message, self::SEVERITY_WARN, $context);
    }
}