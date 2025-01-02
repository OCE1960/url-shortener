<?php

namespace App\Services;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Throwable;

class EventLoggerService
{

    public static function errorLogger(Throwable $e): void
    {

        $requestId = uniqid();
        $log = self::logger();

        $log->error($e->getMessage(), ['RequestId' => $requestId, 'trace' => $e]);
    }

    public static function infoLogger(string $message): void
    {

        $requestId = uniqid();
        $log = self::logger();

        $log->info($message, ['RequestId' => $requestId]);
    }

    public static function logger(): Logger
    {
        $log = new Logger('atarim');
        $log->pushHandler(new StreamHandler(__DIR__ . "/../../log/application.txt", Level::Info));
        $log->pushHandler(new FirePHPHandler());

        return $log;
    }
}
