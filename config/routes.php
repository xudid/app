<?php

use App\App;

return [
    [
        'method' => 'GET',
        'path' => '/',
        'name' => 'default',
        'callback' => function () {
            return App::render('Default route, please add route in config/routes.php ');
        },
    ],
];
