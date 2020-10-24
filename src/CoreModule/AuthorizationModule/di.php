<?php

use App\CoreModule\AuthModule\AuthController;
use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;
use App\CoreModule\AuthorizationModule\Middleware\AuthorizationMiddleware;
use function Di\create;
use function DI\get;


return [
    AuthorizationController::class => create(AuthorizationController::class),
    AuthorizationMiddleware::class => create(AuthorizationMiddleware::class)->constructor(get(AuthController::class), get(AuthorizationController::class))
];
