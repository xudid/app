<?php

use App\CoreModule\AuthorizationModule\Middleware\AuthorizationMiddleware;
use Middleware\ControllerDispatcher;

return [
    'app_name' => 'backoffice',
    'root_dir' => dirname($_SERVER['DOCUMENT_ROOT']),
    'config_dir' => dirname($_SERVER['DOCUMENT_ROOT']) .DIRECTORY_SEPARATOR.'config',
    'temp_dir' => dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'tmp',
    'site_modules' => 'modules.php',

    'core_modules' => [
        App\CoreModule\SetupModule\SetupModule::class,
        App\CoreModule\LoggingModule\LoggingModule::class,
        App\CoreModule\RouterModule\RouterModule::class,
        App\CoreModule\AuthModule\AuthModule::class,
        App\CoreModule\AuthorizationModule\AuthorizationModule::class,
        App\CoreModule\ManagerModule\ManagerModule::class,
        App\CoreModule\RoleModule\RoleModule::class,
        App\CoreModule\UserModule\UserModule::class,
    ],
    'app_modules' => [
        App\Articles\ArticlesModule::class,
        App\Stock\StockModule::class,
        App\Planning\GammeModule\GammeModule::class,
        App\Planning\PhaseModule\PhaseModule::class,
        App\Planning\SequencingModule\SequencingModule::class,
    ],

    'pipeline' =>[
        'router_middleware',
        AuthorizationMiddleware::class,
        ControllerDispatcher::class
     ],

    'mail_accounts' => [
        'default' => [
            'host' => '192.168.226.56',
            'port' => '1025',
            'user_name' => null,
            'password' => null,
        ]
    ],
    'mail_senders' => [
        'default' => [
            'email' => 'no-reply@mowjo.fr',
            'name' => 'no-reply',
        ]
    ]

];