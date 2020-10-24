<?php

use App\CoreModule\AuthModule\AuthController;

return [
    AuthController::class => \DI\create(AuthController::class)
];
