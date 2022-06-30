<?php

use App\App;
use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;
use App\CoreModule\RoleModule\Controller\RolesController;

return [
	[
		'action' => [
			'description' => 'List roles',
			'type' => 'LIST'
		],
		'method' => 'GET',
		'name' => 'roles',
		'path' => '/roles',
		'callback' => function () {
			return App::render(App::get(RolesController::class)->index());
		},
	],
	[
		'action' => [
			'description' => 'Search roles',
			'type' => 'SEARCH'
		],
		'method' => 'GET',
		'path' => '/roles/search',
		'name' => 'roles_search',
		'callback' => function () {
			return App::render(App::get(RolesController::class)->search());
		},
	],
	[
		'action' => [
			'description' => 'Search roles',
			'type' => 'SEARCH'
		],
		'method' => 'POST',
		'path' => '/roles/search',
		'name' => 'roles_search',
		'callback' => fn() => App::render(App::get(RolesController::class)->search())
	],

	[
		'action' => [
			'description' => 'Show a role informations',
			'type' => 'SHOW'
		],
		'method' => 'GET',
		'name' => 'roles_show',
		'path' => '/roles/:id',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::render(App::get(RolesController::class)->show([$id]));
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Form to register a new role',
			'type' => 'NEW'
		],
		'method' => 'GET',
		'name' => 'roles_new',
		'path' => '/roles/new',
		'callback' => function () {
			return App::get(RolesController::class)->new();
		},
	],
	[
		'action' => [
			'description' => 'Persist a new role',
			'type' => 'ADD'
		],
		'method' => 'POST',
		'path' => '/roles/create',
		'name' => 'roles_create',
		'callback' => function () {
			App::get(RolesController::class)->create();
		},
	],
	[
		'action' => [
			'description' => 'Form to modify a role informations',
			'type' => 'MODIFY'
		],
		'method' => 'GET',
		'name' => 'roles_edit',
		'path' => '/roles/:id/edit',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::render(App::get(RolesController::class)->edit([$id]));
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Update role informations',
			'type' => 'UPDATE'
		],
		'method' => 'POST',
		'path' => '/roles/:id/update',
		'name' => 'roles_update',
		'callback' => function ($params) {
			App::get(RolesController::class)->update($params['id']);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Delete a role ',
			'type' => 'DELETE'
		],
		'method' => 'GET',
		'path' => '/roles/:id/delete',
		'name' => 'roles_delete',
		'callback' => function ($params) {
			$id = $params['id'];
			App::get(RolesController::class)->delete($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Delete a role ',
			'type' => 'DELETE'
		],
		'method' => 'POST',
		'path' => '/roles/:id/delete',
		'name' => 'roles_delete',
		'callback' => function ($params) {
			$id = $params['id'];
			App::get(RolesController::class)->delete($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Edit modules allowed to a role ',
			'type' => 'NEW'
		],
		'method' => 'GET',
		'path' => '/roles/:id/modules',
		'name' => 'roles_modules_new',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(AuthorizationController::class)->authorizeRole($params['id']);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Edit modules allowed to a role ',
			'type' => 'NEW'
		],
		'method' => 'GET',
		'path' => "/role/:id/modules-authorizations",
		'name' => 'roles_modules',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(AuthorizationController::class)->authorizeRole($params['id']);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Persist a module allowed to a role',
			'type' => 'ADD'
		],
		'method' => 'POST',
		'path' => "/roles/:id/module/add",
		'name' => 'roles_add_modules',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(RolesController::class)->addModules($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Remove a module allowed to a role',
			'type' => 'DELETE'
		],
		'method' => 'POST',
		'path' => "/roles/:roles_id/modules/:modules_id/delete",
		'name' => 'roles_delete_modules',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(RolesController::class)->deleteModules($params['roles_id'], $params['modules_id']);
		},
		'params' => [
			['roles_id' => '[0-9]+'],
			['modules_id' => '[0-9]+']
		]
	],
	[
		'action' => [
			'description' => 'Form to allow a module action',
			'type' => 'NEW'
		],
		'method' => 'POST',
		'path' => '/roles/:role_id/module/:module_id/actions',
		'name' => 'roles_actions_new',
		'callback' => function ($params) {
			return App::get(AuthorizationController::class)->authorizeModuleActions($params['role_id'], $params['module_id']);
		},
		'params' => [
			['role_id' => '[0-9]+'],
			['module_id' => '[0-9]+']
		]
	],
	[
		'action' => [
			'description' => 'Form to allow a module action',
			'type' => 'NEW'
		],
		'method' => 'GET',
		'path' => "/roles/:role_id/module/:module_id/actions/edit",
		'name' => 'roles_actions',
		'callback' => function ($params) {
			return App::get(RolesController::class)->editActions($params['role_id'], $params['module_id']);
		},
		'params' => [
			['role_id' => '[0-9]+'],
			['module_id' => '[0-9]+']
		]
	],
	[
		'action' => [
			'description' => 'Persist action role attribution',
			'type' => 'ADD'
		],
		'method' => 'POST',
		'path' => "/roles/:role_id/module/:module_id/actions/add",
		'name' => 'roles_add_actions',
		'callback' => function ($params) {
			return App::get(RolesController::class)->addActions($params['role_id'], $params['module_id']);
		},
		'params' => [
			['role_id' => '[0-9]+'],
			['module_id' => '[0-9]+']
		]
	],
	[
		'action' => [
			'description' => 'Remove action role attribution',
			'type' => 'DELETE'
		],
		'method' => 'POST',
		'path' => "/roles/:roles_id/actions/:actions_id/delete",
		'name' => 'roles_delete_actions',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(RolesController::class)->deleteActions($params['roles_id'], $params['actions_id']);
		},
		'params' => [
			['roles_id' => '[0-9]+'],
			['actions_id' => '[0-9]+']
		]
	],
];
