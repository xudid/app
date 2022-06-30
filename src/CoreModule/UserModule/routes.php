<?php

use App\App;
use App\CoreModule\UserModule\Controller\UsersController;

return [
	[
		'action' => [
			'description' => 'List users',
			'type' => 'LIST'
		],
		'method' => 'GET',
		'name' => 'users',
		'path' => '/users',
		'callback' => function () {
			return App::get(UsersController::class)->index();
		}
	],
	[
		'action' => [
			'description' => 'New user form',
			'type' => 'NEW'
		],
		'method' => 'GET',
		'name' => 'users_new',
		'path' => '/users/new',
		'callback' => function () {
			return App::get(UsersController::class)->new();
		}
	],
	[
		'action' => [
			'description' => 'Persist new user',
			'type' => 'ADD'
		],
		'method' => 'POST',
		'path' => '/users/create',
		'name' => 'users_create',
		'callback' => function () {
			App::get(UsersController::class)->create();
		}
	],
	[
		'action' => [
			'description' => 'Show user',
			'type' => 'SHOW'
		],
		'method' => 'GET',
		'name' => 'users_show',
		'path' => '/users/:id',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(UsersController::class)->show($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Show logged user',
			'type' => 'SELF'
		],
		'method' => 'GET',
		'name' => 'users_self',
		'path' => '/users/myaccount',
		'callback' => function () {
			return App::get(UsersController::class)->editSelf();
		},
	],
	[
		'action' => [
			'description' => 'Edit user form',
			'type' => 'EDIT'
		],
		'method' => 'GET',
		'name' => 'users_edit',
		'path' => '/users/:id/edit',
		'callback' => function ($params) {
			$id = $params['id'];
			return App::get(UsersController::class)->edit($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Update user data',
			'type' => 'UPDATE'
		],
		'method' => 'POST',
		'path' => '/users/:id/update',
		'name' => 'users_update',
		'callback' => function ($params) {
			App::get(UsersController::class)->update($params['id']);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Delete user data',
			'type' => 'DELETE'
		],
		'method' => 'POST',
		'path' => '/users/:id/delete',
		'name' => 'users_delete',
		'callback' => function ($id) {
			App::get(UsersController::class)->delete($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Delete user data',
			'type' => 'DELETE'
		],
		'method' => 'GET',
		'path' => '/users/:id/delete',
		'name' => 'users_delete',
		'callback' => function ($id) {
			App::get(UsersController::class)->delete($id);
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Search users',
			'type' => 'SEARCH'
		],
		'method' => 'GET',
		'path' => '/users/search',
		'name' => 'users_search',
		'callback' => function () {
			return App::render(App::get(UsersController::class)->search());
		},
	],
	[
		'action' => [
			'description' => 'Search users',
			'type' => 'SEARCH'
		],
		'method' => 'POST',
		'path' => '/users/search',
		'name' => 'users_search',
		'callback' => fn() => App::render(App::get(UsersController::class)->search())
	],
	[
		'action' => [
			'description' => 'Edit user roles',
			'type' => 'MODIFY'
		],
		'method' => 'GET',
		'path' => "/users/:id/roles/edit",
		'name' => 'users_roles',
		'callback' => function ($params) {
			return App::render(App::get(UsersController::class)->editRoles($params['id']));
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Edit user roles',
			'type' => 'MODIFY'
		],
		'method' => 'GET',
		'path' => "/users/:id/roles/edit",
		'name' => 'users_roles_new',
		'callback' => function ($params) {
			return App::render(App::get(UsersController::class)->editRoles($params['id']));
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Persist user roles',
			'type' => 'ADD'
		],
		'method' => 'POST',
		'path' => "/users/:id/roles/add",
		'name' => 'users_add_roles',
		'callback' => function ($params) {
			return App::render(App::get(UsersController::class)->addRole($params['id']));
		},
		'params' => [['id' => '[0-9]+']]
	],
	[
		'action' => [
			'description' => 'Delete user role',
			'type' => 'DELETE'
		],
		'method' => 'POST',
		'path' => "/users/:users_id/roles/:roles_id/delete",
		'name' => 'users_delete_roles',
		'callback' => function ($params) {
			return App::render(App::get(UsersController::class)->deleteRole($params['users_id'], $params['roles_id']));
		},
		'params' => [
			['users_id' => '[0-9]+'],
			['roles_id' => '[0-9]+'],
		]
	],
];
