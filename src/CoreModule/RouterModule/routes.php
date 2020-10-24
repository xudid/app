<?php

use App\App;
use App\CoreModule\RouterModule\Controller\RouterController;

return [
    [
        'method' => 'GET',
        'name' => 'routes_index',
        'path' => '/routes',
        'callback' => function () {
            $controller = App::get(RouterController::class);
            return App::render($controller->index());
        }
    ],
    [
        'method' => 'GET',
        'name' => 'routes_name',
        'path' => '/routes/GET/:name',
        'callback' => function ($name) {
            $controller = App::get(RouterController::class);
            return App::render($controller->show('GET', $name['name']));
        },
        'params' => [['name' => '[\w]+']]
    ],
    [
        'method' => 'GET',
        'name' => 'routes_scope_post',
        'path' => '/routes',
        'callback' => function ($name) {
            $controller = App::get(RouterController::class);
            return App::render($controller->routesByMethod('POST', $name['name']));
        },
        'params' => [['name' => '[\w]+']]
    ],
];
