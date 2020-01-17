<?php

namespace App\CoreModule\AuthModule;


use App\App;
use App\Module\Module;
use Psr\Container\ContainerInterface;

/**
 * Class AuthModule
 * @package App\CoreModule\AuthModule
 */
class AuthModule extends Module
{
    protected $scope = "auth";
    private $controller = null;

    /**
     * [__construct description]
     * @param ContainerInterface $container [description]
     */
    public function __construct(App $app)
    {
        $this->name = 'module_auth';
        $logger = $app->get('logger');
        $logger->info('initializing authentification module');
        $this->controller = new AuthController($app);
        $controller = $this->controller;
        $router = $app->get('router');
        parent::__construct($app);

        if (!$this->controller->isloggedin()) {
            $this->setModuleInfo("text", "Login", "", "/login", "right");
        } else {
            $this->setModuleInfo("text", "Logout", "", "/logout", "right");
        }


        $router->get('', '/login', 'login',function ($view) use ($app, $logger,$controller) {
            $logger->info('calling login');
            return $app->render($controller->login());
        });


        $router->get('', '/logout', 'logout',function ($view) use ($app, $controller) {
            return $app->render($controller->logout());
        });


        $router->post('', '/auth', 'auth',function ($view) use ($app, $controller) {
            $controller->auth($app);
        });

        /*$firewall->withRule("IPV6", "::1", "/login", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/auth", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/logout", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/demo", ["ACCEPT" => null])
            ->withRule("IPV6", "::1", "/demo", ["LOG" => null]);
    }*/
    }
}

