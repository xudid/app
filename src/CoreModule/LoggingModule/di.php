<?php

use App\App;
use App\CoreModule\LoggingModule\Controller\LoggingController;
use App\CoreModule\LoggingModule\FileManager;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use function DI\create;
use function DI\get;

return [
    'displayed_logs'=> 30,
	'log_file' => App::tempDir() . DIRECTORY_SEPARATOR . 'app.log',
    'line_output' => "%datetime% > %level_name% > %message% > %context% > %extra%\n",
    'dateFormat' => "Y-m-d\TH:i:sP",
	StreamHandler::class => create(StreamHandler::class)
		->constructor(get('log_file'), Logger::DEBUG)
        ->method('setFormatter', get(FormatterInterface::class))
    ,
    FormatterInterface::class => create(LineFormatter::class)
        ->constructor(get('line_output'), get('dateFormat')),
	LoggerInterface::class => create(Logger::class)
		->constructor('APPLOG')
		->method('pushHandler', get(StreamHandler::class)),
    LoggingController::class => create(LoggingController::class)
        ->constructor(new FileManager(App::tempDir() . DIRECTORY_SEPARATOR . 'app.log'), get('displayed_logs'))
        ->method('setLogger', get(LoggerInterface::class))
];
