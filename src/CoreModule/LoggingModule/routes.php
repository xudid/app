<?php

use App\App;
use App\CoreModule\LoggingModule\Controller\LoggingController;

return [
    [
        'action' => [
            'description' => 'Show application logs',
            'type' => 'LIST',
        ],
        'method' => 'GET',
        'name' => 'logging',
        'path' => '/logging',
        'callback' => function () {
            $controller = App::get(LoggingController::class);
            return App::render($controller->index());
        }
    ],
    [
        'action' => [
            'description' => 'Clear current log file',
            'type' => 'DELETE',
        ],
        'method' => 'POST',
        'name' => 'logging_clear',
        'path' => '/logging/clear',
        'callback' => function () {
            $controller = App::get(LoggingController::class);
            return App::render($controller->clear());
        }
    ]
];
