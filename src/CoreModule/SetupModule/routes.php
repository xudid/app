<?php

use App\App;
use App\CoreModule\SetupModule\Controller\SetupController;

return [
    [
        'method' => 'GET',
        'name' => 'setup',
        'path' => '/setup',
        'callback' => function () {
            return (App::get(SetupController::class))->setup();
        }
    ],
];
