<?php

use App\CoreModule\LoggingModule\LoggingModule;

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
            App::render('logging index to do');
        }
    ]
];
