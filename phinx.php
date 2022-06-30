<?php

return [
    'paths' =>[
    'migrations' => __DIR__ . '/db/migrations',
    'seeds' => __DIR__ . '/db/seeds'],
    'environments' => [
        'default_migration_table' => 'xudid',
        'default_database' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8'
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'development_db',
            'user' => 'xudid',
            'pass' => 'xudid',
            'port' => '3306',
            'charset' => 'utf8'
        ],

        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8'
        ],

        'version_order' => 'creation'
    ]
];
