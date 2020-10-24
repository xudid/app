<?php

use App\CoreModule\RouterModule\Controller\RouterController;
use Router\Router;
use Router\RouterMiddleware;
use function DI\create;

$router = new Router();
$routerMiddleware = new RouterMiddleware($router);
$router->setAuthorizedMethods(['GET', 'POST']);
return [
    'router' => $router,
    'router_middleware' => $routerMiddleware,
    RouterController::class => create(RouterController::class)
];
