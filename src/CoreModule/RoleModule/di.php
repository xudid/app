<?php

use App\CoreModule\RoleModule\Controller\RolesController;
use function DI\create;

return [
    RolesController::class => create(RolesController::class)
];