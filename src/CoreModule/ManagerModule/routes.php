<?php

use App\App;
use App\CoreModule\ManagerModule\Controller\ModuleManagerController;

return[
    [
        'action' => [
            'description' => 'List modules',
            'type' => 'LIST',
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'modules_index',
        'path' => '/modules',
        'callback' => function() {return (App::get(ModuleManagerController::class)->index());}
    ],
    [
        'action' => [
            'description' => 'Show a module information',
            'type' => 'SHOW',
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'modules_show',
        'path' => '/modules/:id',
        'callback' => function($params) {return (App::get(ModuleManagerController::class))->show($params['id']);},
        'params' => [['id' => '[0-9]+']],
        ],
    [
        'action' => [
            'description' => 'New module form',
            'type' => 'NEW',
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'modules_new',
        'path' => '/modules/new',
        'callback' => function() {return (App::get(ModuleManagerController::class))->new();}
    ],
    [
        'action' => [
            'description' => 'Persist new module',
            'type' => 'ADD'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'POST',
        'name' => 'modules_create',
        'path' => '/modules/create',
        'callback' => function() {return (App::get(ModuleManagerController::class))->create();}
    ],
    [
        'action' => [
            'description' => 'Form to modify modules informations',
            'type' => 'MODIFY'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'modules_edit',
        'path' => '/modules/:id/edit',
        'callback' => function($params) {return (App::get(ModuleManagerController::class))->edit($params['id']);},
        'params' => [['id' => '[0-9]+']],
        ],
    [
        'action' => [
            'description' => 'Update modules informations',
            'type' => 'UPDATE'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'POST',
        'name' => 'modules_update',
        'path' => '/modules/update',
        'callback' => function() {return (App::get(ModuleManagerController::class))->update();},
    ],
    [
        'action' => [
            'description' => 'List a module actions',
            'type' => 'LIST'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'actions_index',
        'path' => '/modules/:modules_id/actions',
        'callback' => function($params) {return (App::get(ModuleManagerController::class)->actions($params['modules_id']));},
        'params' => [['modules_id' => '[0-9]+']],
    ],
    [
        'action' => [
            'description' => 'Form to an action to a module',
            'type' => 'NEW'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'actions_new',
        'path' => '/modules/:modules_id/actions/new',
        'callback' => function($params) {return (App::get(ModuleManagerController::class)->newAction($params['modules_id']));},
        'params' => [['modules_id' => '[0-9]+']],
    ],
    [
        'action' => [
            'description' => 'Persist a new module action',
            'type' => 'ADD'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'POST',
        'name' => 'actions_create',
        'path' => '/modules/:modules_id/actions/create',
        'callback' => function($params) {return (App::get(ModuleManagerController::class)->createAction($params['modules_id']));},
        'params' => [
            ['modules_id' => '[0-9]+'],
        ],
    ],
    [
        'action' => [
            'description' => 'Show a module action informations',
            'type' => 'SHOW'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'actions_show',
        'path' => '/modules/:modules_id/actions/:actions_id',
        'callback' => function($params) {return (App::get(ModuleManagerController::class)->showAction($params['modules_id'], $params['actions_id']));},
        'params' => [
            ['modules_id' => '[0-9]+'],
            ['actions_id' => '[0-9]+'],
        ],
    ],
    [
        'action' => [
            'description' => 'Form to modify a module action',
            'type' => 'MODIFY'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'actions_edit',
        'path' => '/modules/:modules_id/actions/:actions_id/edit',
        'callback' => function($params) {return (App::get(ModuleManagerController::class)->editAction($params['modules_id'], $params['actions_id']));},
        'params' => [
            ['modules_id' => '[0-9]+'],
            ['actions_id' => '[0-9]+'],
        ],
    ],
    [
        'action' => [
            'description' => 'Persist a module action modification',
            'type' => 'UPDATE'
        ],
        'controller' => ModuleManagerController::class,
        'method' => 'GET',
        'name' => 'actions_edit',
        'path' => '/modules/:modules_id/actions/:actions_id/edit',
        'callback' => function($params) {return (App::get(ModuleManagerController::class)->updateAction($params['modules_id'], $params['actions_id']));},
        'params' => [
            ['modules_id' => '[0-9]+'],
            ['actions_id' => '[0-9]+'],
        ],
    ],
];
