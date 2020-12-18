<?php

use App\App;
use App\Page;
use App\View\MainPage;
use Renderer\Renderer;
use function \DI\create;
use function \DI\get;
use Entity\Model\ManagerFactory;
use Entity\Database\DataSourceInterface;
use Entity\Database\Mysql\MysqlDataSource;

$dbConfig = require 'db.php';
$environment = App::getEnvironment();
return [
	'css' => [
		'/css/color.css',
		'/css/view.css',
		'/css/form.css',
		'/css/ui.css',
		'/display.css',
		'/css/collapsible.css',
		'/css/navbar.css',
		'/css/modal.css'
	],
	'js' => [
		'js/app.js',
		'js/collapsible.js'
	],
	'default_allowed_routes' => [
		'default',
		'login',
		'logout',
		'auth',
		'get_reset_token',
		'mail_reset_token',
		'recovery_password',
		'setup',
		'init_firstrole',
		'init_firstuser',
	],
	Page::class => create(MainPage::class),
	DataSourceInterface::class => create(MysqlDataSource::class)
		->constructor('mysql', $dbConfig[$environment]),
	ManagerFactory::class => create(ManagerFactory::class)
		->constructor(get(DataSourceInterface::class)),
	Renderer::class => create(Renderer::class),
];
