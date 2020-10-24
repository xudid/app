<?php


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use App\App;
return [
    'app_log_file' => App::tempDir() . DIRECTORY_SEPARATOR . 'app.log',
    StreamHandler::class => DI\create(StreamHandler::class)
        ->constructor(\DI\get('app_log_file'), Logger::DEBUG),
    LoggerInterface::class => \DI\create(Logger::class)
        ->method('pushHandler', \DI\get(StreamHandler::class))
];
