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
	[
		'method' => 'GET',
		'name' => 'init_firstrole',
		'path' => '/init/firstrole',
		'callback' => function () {
			return (App::get(SetupController::class))->initGodRole();
		}
	],
	[
		'method' => 'POST',
		'name' => 'init_firstrole',
		'path' => '/init/firstrole',
		'callback' => function () {
			return (App::get(SetupController::class))->initGodRole();
		}
	],
	[
		'method' => 'GET',
		'name' => 'init_firstuser',
		'path' => '/init/firstuser/:id',
		'params' => [['id' => '[0-9]+']],
		'callback' => function ($params) {

			return (App::get(SetupController::class))->initRoot($params['id']);
		}
	],
	[
		'method' => 'POST',
		'name' => 'init_firstuser',
		'path' => '/init/firstuser',
		'callback' => function () {
			return (App::get(SetupController::class))->initRoot(0);
		}
	],
];
