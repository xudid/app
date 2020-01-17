<?php


namespace App\CoreModule\ManagerModule;


use App\App;
use App\CoreModule\RouterModule\Controller\ModuleManagerController;
use App\Module\Module;

class ManagerModule extends Module
{
    public function __construct(App $app)
    {
        $this->name = 'module manager';
        $logger = $app->get('logger');
        $logger->info('initializing manager module');
        parent::__construct($app);
        $this->controller = new ModuleManagerController($app);
        $controller = $this->controller;
        $router = $app->get('router');
        $router->get('modules','/modules','modules_index',function() use($controller, $app){return $app->render($controller->index($app));});
        $logger->info('route for module index added');
        $logger->info('module manager initialized');
    }
}