<?php
use App\App;
use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;

return [
    [
		'action' => [
			'description' => 'Edit role authorizations',
			'type' => 'MODIFY'
		],
        'method' => 'GET',
        'path' => "/authorizations/role/:id",
        'name' => 'authorizations_roles_edit',
        'callback' => function ($params) {
            return App::get(AuthorizationController::class)->authorizeRole($params['id']);
        },
        'params' => [['id' => '[0-9]+']]
    ],
    [
		'action' => [
			'description' => 'Edit role authorizations',
			'type' => 'UPDATE'
		],
        'method' => 'POST',
        'path' => "/authorizations/modules",
        'name' => 'authorizations_roles_register',
        'callback' => function () {
            return App::get(AuthorizationController::class)->registerModulesAuthorizations();
        },
    ],
    [
		'action' => [
			'description' => 'Edit role actions authorizations',
			'type' => 'MODIFY'
		],
        'method' => 'GET',
        'path' => "/authorizations/role/:roles_id/module/:modules_id/actions",
        'name' => 'authorizations_role_module_actions_edit',
        'callback' => function ($params) {
            return App::get(AuthorizationController::class)->authorizeModuleActions($params['roles_id'], $params['modules_id']);
        },
        'params' => [
                        ['roles_id' => '[0-9]+'],
                        ['modules_id' => '[0-9]+'],
            ]
    ],
    [
		'action' => [
			'description' => 'Edit role actions authorizations',
			'type' => 'UPDATE'
		],
        'method' => 'POST',
        'path' => '/authorizations/actions',
        'name' => 'authorizations_role_actions_register',
        'callback' => function () {
            return App::get(AuthorizationController::class)->registerActionsAuthorizations();
        },
    ],
];