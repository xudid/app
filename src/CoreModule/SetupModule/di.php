<?php

use App\CoreModule\SetupModule\SetupController;
use function DI\create;

return [
   SetupController::class => create(SetupController::class)
];
