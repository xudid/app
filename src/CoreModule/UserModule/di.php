<?php

use App\CoreModule\UserModule\Controller\UsersController;
use function Di\create;

return [
    UsersController::class => create(UsersController::class),
];
