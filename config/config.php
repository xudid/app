<?php

return[
    'app_name' => 'backoffice',
    'config_dir' => dirname($_SERVER['DOCUMENT_ROOT']) .DIRECTORY_SEPARATOR.'config',
    'temp_dir' => dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'tmp',
    'modules' => [
        App\CoreModule\LoggingModule\LoggingModule::class,
        App\CoreModule\RouterModule\RouterModule::class,
        App\CoreModule\ManagerModule\ManagerModule::class,
        App\CoreModule\RendererModule\RendererModule::class,
        App\CoreModule\AuthModule\AuthModule::class
    ],

    'pipeline' =>[
        'router_middleware',
        'controller_dispatcher'
     ]

];