<?php

use App\Pipeline\Pipeline;
use App\Security\TokenProvider;
use Core\Contracts\DataSourceInterface;
use Entity\Database\Mysql\MysqlDataSource;
use Entity\Database\QueryBuilder\QueryBuilder;
use Entity\Model\ManagerFactory;
use Renderer\Renderer;
use function DI\create;
use function DI\get;

return[
    'app_name' => 'backoffice',
    'config_dir' => $_SERVER['DOCUMENT_ROOT'] . '/config',
    'css' => [
        '/css/ui.css',
        "https://fonts.googleapis.com/icon?family=Material+Icons"
    ],
    'js' => [
        '/js/jquery.js',
        '/js/collapsible.js',
        '/js/app.js',
        '/js/modal.js',
    ],
    'mysql_datasource_params' => [
        'mysql.server' => 'localhost',
        'mysql.port' => '3306',
        'mysql.database' => 'brickdb',
        'mysql.user' => 'xudid',
        'mysql.password' => 'xudid',
        'mysql.attributes' => [],

    ],
    DataSourceInterface::class => MysqlDataSource::class,
    MysqlDataSource::class => create()
        ->constructor('mysql', get('mysql_datasource_params')),
    'model_manager_factory' => create(ManagerFactory::class)->constructor(get(MysqlDataSource::class)),
    Middleware\ControllerDispatcher::class => create(\Middleware\ControllerDispatcher::class),
    Pipeline::class => create(App\Pipeline\Pipeline::class),
    Renderer::class => create(Renderer::class),
    TokenProvider::class => create(TokenProvider::class),
    QueryBuilder::class => create(QueryBuilder::class)
        ->constructor(get(MysqlDataSource::class)),
    ];