<?php

use App\App;
use App\CoreModule\RouterModule\Controller\RouterController;

return [
	[
		'action' => [
			'description' => 'List routes',
			'type' => 'LIST'
		],
		'method' => 'GET',
		'name' => 'routes',
		'path' => '/routes',
		'callback' => function () {
			$controller = App::get(RouterController::class);
			return App::render($controller->index());
		}
	],
];
