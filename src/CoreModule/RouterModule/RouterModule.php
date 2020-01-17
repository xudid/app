<?php


namespace App\CoreModule\RouterModule;


use App\App;
use App\Module\Module;
use Psr\Container\ContainerInterface;
use Router\Router;
use Router\RouterMiddleware;

class RouterModule extends Module
{

    /**
     * RouterModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(App $app)
    {
        $logger = $app->get('logger');
        $logger->info('initializing router module');
        $this->name = 'router';
        $router = new Router();
        $routerMiddleware = new RouterMiddleware($router);
        $router->setAuthorizedMethods(['GET', 'POST']);
        $app->addContainerDefinition('router', $router);
        $logger->info('router initialized and added to container');
        $app->addContainerDefinition('router_middleware', $routerMiddleware);
        $logger->info('router middleware initialized and added to container');
        parent::__construct($app);
        $logger->info('router module initialized');



    }
}