<?php

use Middleware\ControllerDispatcher;

return [
    'app_name' => 'backoffice',
    'root_dir' => dirname($_SERVER['DOCUMENT_ROOT']),
    'config_dir' => dirname($_SERVER['DOCUMENT_ROOT']) .DIRECTORY_SEPARATOR.'config',
    'temp_dir' => dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'tmp',
    'cache_dir' => dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'cache',

    'core_modules' => [
        App\CoreModule\LoggingModule\LoggingModule::class,
        App\CoreModule\RouterModule\RouterModule::class,
    ],
    'app_modules' => [
    ],

    'site_modules' => dirname($_SERVER['DOCUMENT_ROOT']) .DIRECTORY_SEPARATOR.'config' . DIRECTORY_SEPARATOR . 'modules.php',

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