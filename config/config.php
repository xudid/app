<?php

use Middleware\ControllerDispatcher;

return [
    'app_name' => 'backoffice',
    'root_dir' => dirname($_SERVER['DOCUMENT_ROOT']),
    'config_dir' => dirname($_SERVER['DOCUMENT_ROOT']) .DIRECTORY_SEPARATOR.'config',
    'temp_dir' => dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'tmp',
    'site_modules' => 'modules.php',

    'core_modules' => [
        App\CoreModule\LoggingModule\LoggingModule::class,
        App\CoreModule\RouterModule\RouterModule::class,
    ],
    'app_modules' => [
    ],

    'pipeline' =>[
        'router_middleware',
        ControllerDispatcher::class
     ],

    'mail_accounts' => [
        'default' => [
            'host' => '',
            'port' => '',
            'user_name' => '',
            'password' => '',
        ]
    ],
    'mail_senders' => [
        'default' => [
            'email' => '',
            'name' => '',
        ]
    ]

];