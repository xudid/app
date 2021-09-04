<?php

use App\App;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use function DI\create;
use function DI\get;

return [
	'log_file' => App::tempDir() . DIRECTORY_SEPARATOR . 'app.log',
	StreamHandler::class => create(StreamHandler::class)
		->constructor(get('log_file'), Logger::DEBUG),
	LoggerInterface::class => create(Logger::class)
		->constructor('APPLOG')
		->method('pushHandler', get(StreamHandler::class))
];
