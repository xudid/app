<?php

use App\CoreModule\UserModule\Controller\UsersController;
use App\App;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Log\LoggerInterface;
use function DI\create;
use function DI\get;

return [
	UsersController::class => create(UsersController::class)
		->method('setContext', App::getInstance())
		->method('setRouter', get('router'))
		->method('setLogger', get(LoggerInterface::class))
		->method('setRequest', ServerRequest::fromGlobals()),
];
